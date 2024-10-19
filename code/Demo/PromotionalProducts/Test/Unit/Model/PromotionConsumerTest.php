<?php

namespace Demo\PromotionalProducts\Test\Unit\Model;

use Demo\PromotionalProducts\Model\PromotionConsumer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PromotionConsumerTest extends TestCase
{
    protected $promotionConsumer;
    protected $productRepository;
    protected $indexerRegistry;
    protected $dateTime;
    protected $logger;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->indexerRegistry = $this->createMock(IndexerRegistry::class);
        $this->dateTime = $this->createMock(DateTime::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->promotionConsumer = $objectManager->getObject(
            PromotionConsumer::class,
            [
                'productRepository' => $this->productRepository,
                'indexerRegistry' => $this->indexerRegistry,
                'dateTime' => $this->dateTime,
                'logger' => $this->logger
            ]
        );
    }

    /**
     * Test the discount calculation for a given percentage.
     */
    public function testCalculateDiscountedPrice()
    {
        $productPrice = 100;
        $discountPercentage = 20;

        $expectedPrice = $this->promotionConsumer->calculateDiscountedPrice($discountPercentage, $productPrice);

        $this->assertEquals(80, $expectedPrice);
    }

    /**
     * Test if a product is eligible for promotion within a valid date range.
     */
    public function testPromotionEligibility()
    {
        $startDate = '2024-05-01 00:00:00';
        $endDate = '2025-07-01 00:00:00';

        $status = $this->promotionConsumer->calculatePromotionalStatus($startDate, $endDate);

        $this->assertTrue($status);
    }

    /**
     * Test if a product is not eligible for promotion (promotion dates in the past).
     */
    public function testPromotionNotEligible()
    {
        $startDate = '2023-01-01 00:00:00';
        $endDate = '2023-12-31 23:59:59';

        $status = $this->promotionConsumer->calculatePromotionalStatus($startDate, $endDate);

        // Assert that the product is not eligible for promotion
        $this->assertFalse($status);
    }

    /**
     * Test if a product is not eligible for promotion (promotion dates in the future).
     */
    public function testPromotionFuture()
    {
        $startDate = '2026-01-01 00:00:00';
        $endDate = '2026-12-31 23:59:59';

        $status = $this->promotionConsumer->calculatePromotionalStatus($startDate, $endDate);

        // Assert that the product is not eligible for promotion as it is in the future
        $this->assertFalse($status);
    }
}
