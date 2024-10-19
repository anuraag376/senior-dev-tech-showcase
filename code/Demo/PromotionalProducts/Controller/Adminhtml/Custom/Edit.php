<?php

namespace Demo\PromotionalProducts\Controller\Adminhtml\Custom;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 *
 * Controller for handling the promotional product edit page in the admin panel.
 */
class Edit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute the action to load and render the edit page.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Create result page instance
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        // Set the page title
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Promotional Product'));

        return $resultPage;
    }
}
