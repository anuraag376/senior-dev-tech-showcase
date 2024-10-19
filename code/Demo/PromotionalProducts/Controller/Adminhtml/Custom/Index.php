<?php

namespace Demo\PromotionalProducts\Controller\Adminhtml\Custom;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends Action implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Demo_PromotionalProducts::grids');
        $resultPage->addBreadcrumb(__('Grids'), __('Grids'));
        $resultPage->addBreadcrumb(__('Custom'), __('Custom'));
        $resultPage->getConfig()->getTitle()->prepend(__('Promotional Products'));
        return $resultPage;
    }
}
