<?php

namespace Demo\PromotionalProducts\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;

/**
 * Class PromotionalProducts
 *
 * Block class to fetch and display promotional products.
 */
class PromotionalProducts extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the collection of promotional products.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getPromotionalProducts()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('special_price', ['neq' => null]);
        $collection->addAttributeToFilter('special_from_date', ['lteq' => date('Y-m-d H:i:s')]);
        $collection->addAttributeToFilter('special_to_date', ['gteq' => date('Y-m-d H:i:s')]);

        return $collection;
    }
}
