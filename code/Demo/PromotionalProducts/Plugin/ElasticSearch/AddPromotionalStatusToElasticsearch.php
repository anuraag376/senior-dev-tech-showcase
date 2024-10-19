<?php

namespace Demo\PromotionalProducts\Plugin\ElasticSearch;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class AddPromotionalStatusToElasticsearch
 *
 * Plugin to add promotional status to Elasticsearch product data during indexing.
 */
class AddPromotionalStatusToElasticsearch
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param ProductRepositoryInterface $productRepository
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Add 'promotional_status' to the Elasticsearch product document before mapping.
     *
     * @param ProductDataMapper $subject
     * @param array $documentData
     * @param int $storeId
     * @return array
     */
    public function beforeMap(ProductDataMapper $subject, $documentData, $storeId)
    {
        foreach ($documentData as $productId => &$productData) {
            try {
                // Fetch product data and calculate promotional status
                $product = $this->productRepository->getById($productId);

                $promotionStartDate = $product->getSpecialFromDate();
                $promotionEndDate = $product->getSpecialToDate();
                $specialPrice = $product->getSpecialPrice();

                // Determine promotional status
                $promotionalStatus = $this->isProductInPromotion($specialPrice, $promotionStartDate, $promotionEndDate);
                $productData['promotional_status'] = $promotionalStatus;

            } catch (\Exception $e) {
                // Log error or continue silently
                $this->logger->error('Error adding promotional status to product: ' . $e->getMessage());
                continue;
            }
        }

        return [$documentData, $storeId];
    }

    /**
     * Check if the product is currently in a promotion.
     *
     * @param float|null $specialPrice
     * @param string|null $promotionStartDate
     * @param string|null $promotionEndDate
     * @return bool
     */
    public function isProductInPromotion($specialPrice, $promotionStartDate, $promotionEndDate)
    {
        $currentDate = $this->dateTime->gmtDate();
        if ($promotionStartDate && $promotionEndDate && $specialPrice) {
            return $currentDate >= $promotionStartDate && $currentDate <= $promotionEndDate;
        }
        return false;
    }
}
