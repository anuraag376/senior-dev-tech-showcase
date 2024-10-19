<?php

namespace Demo\PromotionalProducts\Controller\Adminhtml\Custom;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassEnable
 *
 * Admin controller to handle enabling multiple products in bulk.
 */
class MassEnable extends Action implements HttpPostActionInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Action\Context $context,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Execute action to enable selected products.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // Get product IDs from the request (passed from UI)
        $productIds = $this->getRequest()->getParam('selected', []); // Array of product IDs
        $enabledCount = 0;

        try {
            foreach ($productIds as $productId) {
                // Load each product by ID and set it to enabled
                $product = $this->productRepository->getById($productId);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED); // Enable product
                $this->productRepository->save($product);
                $enabledCount++;
            }
            // Success message after enabling products
            $this->messageManager->addSuccessMessage(__('%1 product(s) have been enabled.', $enabledCount));
        } catch (\Exception $e) {
            // Error handling
            $this->messageManager->addErrorMessage(__('An error occurred while enabling the products.'));
        }

        // Redirect to the referring page
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
    }
}
