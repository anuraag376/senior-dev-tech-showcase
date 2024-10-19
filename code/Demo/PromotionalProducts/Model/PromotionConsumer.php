<?php

namespace Demo\PromotionalProducts\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class PromotionConsumer
 *
 * Handles processing promotional product updates from a message queue.
 */
class PromotionConsumer
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Constructor
     *
     * @param ProductRepositoryInterface $productRepository
     * @param IndexerRegistry $indexerRegistry
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        IndexerRegistry $indexerRegistry,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->indexerRegistry = $indexerRegistry;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Process promotional product updates from the message queue.
     *
     * @param string $message
     * @return bool
     */
    public function process($message)
    {
        try {
            $data = json_decode($message, true);

            $productId = $data['entity_id'];
            $product = $this->productRepository->getById($productId);
            $price = $product->getPrice();

            // Format the promotion dates
            $formattedFromDate = $this->dateTime->date('Y-m-d H:i:s', strtotime($data['special_from_date']));
            $formattedToDate = $this->dateTime->date('Y-m-d H:i:s', strtotime($data['special_to_date']));

            // Calculate the special price based on the discount percentage
            $special_price = $this->calculateDiscountedPrice($data['discount_percentage'], $price);

            // Set the promotional data on the product
            $product->setData('special_price', $special_price);
            $product->setData('special_from_date', $formattedFromDate);
            $product->setData('special_to_date', $formattedToDate);

            // Save the product with updated promotion data
            $this->productRepository->save($product);

            // Update promotional status and Elasticsearch index
            $promotionalStatus = $this->calculatePromotionalStatus($data['special_from_date'], $data['special_to_date']);
            $this->updateProductInElasticsearch($productId);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Error processing message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate promotional status based on the given promotion dates.
     *
     * @param string $start_Date
     * @param string $end_Date
     * @return bool
     */
    public function calculatePromotionalStatus($start_Date, $end_Date)
    {
        $startDate = date('Y-m-d H:i:s', strtotime($start_Date));
        $endDate = date('Y-m-d H:i:s', strtotime($end_Date));
        $currentDate = date('Y-m-d H:i:s');

        return ($startDate && $endDate && ($currentDate >= $startDate && $currentDate <= $endDate));
    }

    /**
     * Calculate the discounted price based on the discount percentage.
     *
     * @param float $discount_percentage
     * @param float $price
     * @return float
     */
    public function calculateDiscountedPrice($discount_percentage, $price)
    {
        return $price - (($discount_percentage / 100) * $price);
    }

    /**
     * Update the product in Elasticsearch and reindex it.
     *
     * @param int $productId
     * @return void
     */
    private function updateProductInElasticsearch($productId)
    {
        $indexer = $this->indexerRegistry->get('catalogsearch_fulltext');
        $indexer->reindexRow($productId);
    }
}
