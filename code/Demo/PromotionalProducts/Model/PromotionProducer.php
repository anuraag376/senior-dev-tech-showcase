<?php

namespace Demo\PromotionalProducts\Model;

use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Class PromotionProducer
 *
 * Responsible for sending product promotion updates to the message queue (RabbitMQ).
 */
class PromotionProducer
{
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * Constructor
     *
     * @param PublisherInterface $publisher
     */
    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * Send the product promotion update to RabbitMQ.
     *
     * @param array $productData
     * @return void
     */
    public function sendPromotionUpdate($productData)
    {
        // Send the product promotion data as a JSON string to RabbitMQ
        $this->publisher->publish('promotion.update', json_encode($productData));
    }
}
