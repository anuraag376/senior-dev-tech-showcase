<?php

namespace Demo\PromotionalProducts\Controller\Adminhtml\Custom;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Demo\PromotionalProducts\Model\PromotionProducer;

/**
 * Class Save
 *
 * Controller responsible for saving promotional product data and sending updates.
 */
class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var PromotionProducer
     */
    protected $promotionProducer;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param PromotionProducer $promotionProducer
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        PromotionProducer $promotionProducer
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->promotionProducer = $promotionProducer;
        parent::__construct($context);
    }

    /**
     * Execute the save action and trigger promotion updates.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // Retrieve form data from the request
        $data = $this->getRequest()->getPostValue();

        // Send promotion updates
        $this->promotionProducer->sendPromotionUpdate($data);

        // Redirect back to the index page
        return $this->_redirect('*/*/index');
    }
}
