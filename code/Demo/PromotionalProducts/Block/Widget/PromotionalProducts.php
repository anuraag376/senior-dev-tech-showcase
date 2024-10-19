<?php

namespace Demo\PromotionalProducts\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class PromotionalProducts
 *
 * Widget block class for displaying promotional products.
 */
class PromotionalProducts extends AbstractProduct implements BlockInterface
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Template for the widget block.
     *
     * @var string
     */
    protected $_template = 'Demo_PromotionalProducts::widget/promotionalproducts.phtml';

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve collection of promotional products.
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
