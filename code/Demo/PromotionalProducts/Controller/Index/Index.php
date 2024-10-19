<?php

namespace Demo\PromotionalProducts\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * Controller for rendering the promotional products page on the frontend.
 */
class Index extends Action
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
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute action to render the promotional products page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Create and configure the result page
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Promotional Products'));

        return $resultPage;
    }
}
