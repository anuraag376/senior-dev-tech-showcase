<?php

declare(strict_types=1);

namespace Demo\PromotionalProducts\Ui\Component\DataProvider;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class Custom
 *
 * Provides data for the UI component, specifically handling promotional products.
 */
class Custom extends UiDataProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var ReportingInterface
     */
    protected $reporting;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezone
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->collection = $collectionFactory->create();
        $this->timezone = $timezone;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Retrieve the collection of products with special prices.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getCollection()
    {
        $this->collection->addAttributeToSelect([
            'sku', 'name', 'price', 'special_price', 'special_from_date', 'special_to_date', 'status'
        ])->addFieldToFilter('special_price', ['gt' => 0]);

        return $this->collection;
    }

    /**
     * Get data for the UI component, including special price and promotional status.
     *
     * @return array
     */
    public function getData(): array
    {
        try {
            if (!$this->getCollection()->isLoaded()) {
                $this->getCollection()->load();
            }

            $items = [];
            $currentDate = $this->timezone->date()->format('Y-m-d'); // Get current date in Y-m-d format
            foreach ($this->getCollection() as $product) {
                $specialFromDate = $product->getSpecialFromDate();
                $specialToDate = $product->getSpecialToDate();
                $specialPrice = $product->getSpecialPrice();
                $status = $product->getStatus();

                // Determine if the product is currently in promotion
                if ($specialFromDate && $specialToDate && $specialPrice && $status == 1) {
                    $isActive = $currentDate >= $specialFromDate && $currentDate <= $specialToDate;
                } else {
                    $isActive = false;
                }

                $items[] = [
                    'id'            => $product->getId(),
                    'sku'           => $product->getSku(),
                    'name'          => $product->getName(),
                    'price'         => $product->getPrice(),
                    'special_price' => $product->getSpecialPrice(),
                    'status'        => $isActive ? 'Active' : 'Inactive',
                ];
            }

            return [
                'totalRecords' => $this->getCollection()->getSize(),
                'items'        => array_values($items),
            ];
        } catch (LocalizedException $e) {
            // Catch any exceptions and return an error message
            return [
                'items' => [],
                'error' => 'Server Error: Please contact the administrator if it persists !!',
            ];
        }
    }
}
