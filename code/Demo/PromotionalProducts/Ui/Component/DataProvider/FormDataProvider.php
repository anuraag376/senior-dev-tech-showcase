<?php
namespace Demo\PromotionalProducts\Ui\Component\DataProvider;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class FormDataProvider
 *
 * Provides product form data, including regular price, special price, and discount percentage.
 */
class FormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $productCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $productCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $productCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get product data, including discount percentage calculation.
     *
     * @return array
     */
    public function getData(): array
    {
        // Select necessary attributes for the product collection
        $item = $this->collection
            ->addAttributeToSelect(['name', 'special_from_date', 'special_to_date', 'price', 'special_price'])
            ->load()
            ->getFirstItem();

        $this->loadedData = [];

        // Retrieve product data
        $data = $item->getData();

        // Get regular price and special price
        $regularPrice = $item->getPrice(); // Regular price
        $specialPrice = $item->getSpecialPrice(); // Special price

        // Calculate the discount percentage if both prices are available
        if ($regularPrice && $specialPrice && $regularPrice > $specialPrice) {
            $discountPercentage = (($regularPrice - $specialPrice) / $regularPrice) * 100;
            $data['discount_percentage'] = round($discountPercentage, 2); // Round to 2 decimal places
        } else {
            $data['discount_percentage'] = 0; // No discount
        }

        // Store the modified data in the loadedData array
        $this->loadedData[$item->getId()] = $data;

        return $this->loadedData;
    }
}
