<?php
declare(strict_types=1);
namespace Tests\App\Service;

use App\Model\Basket;
use App\Model\Product;
use App\Repository\InMemoryProductRepository;
use App\Service\BasketService;
use App\Strategy\LowOrderDelivery;
use App\Strategy\MediumOrderDelivery;
use App\Strategy\FreeDelivery;
use App\Strategy\RedWidgetDiscount;
use PHPUnit\Framework\TestCase;
use SlimSession\Helper as Session;

class BasketServiceTest extends TestCase
{
    private InMemoryProductRepository $productRepository;
    private Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        $this->session = new Session();
        $this->productRepository = new InMemoryProductRepository();
    }

    protected function tearDown(): void
    {
        session_destroy();
        parent::tearDown();
    }

    /**
     * Provides test cases for basket calculations.
     *
     * @return array
     */
    public static function basketDataProvider(): array
    {
        return [
            'B01, G01 (subtotal $32.90 → $4.95 shipping)' => [['B01', 'G01'], 37.85],
            'R01, R01 (subtotal $49.42 → $4.95 shipping)' => [['R01', 'R01'], 54.37],
            'R01, G01 (subtotal $57.90 → $2.95 shipping)' => [['R01', 'G01'], 60.85],
            'B01, B01, R01, R01, R01 (subtotal $98.27 → Free shipping)' => [['B01', 'B01', 'R01', 'R01', 'R01'], 98.27],
        ];
    }

    /**
     * @dataProvider basketDataProvider
     */
    public function testExampleBaskets(array $products, float $expectedTotal)
    {
        // Calculate subtotal
        $subtotal = array_sum(array_map(fn($code) => $this->productRepository->findByCode($code)->getPrice()->toDollars(), $products));

        // Apply discount before determining the delivery strategy
        $discountStrategy = new RedWidgetDiscount();
        $discount = $discountStrategy->applyDiscount(array_map(fn($code) => $this->productRepository->findByCode($code), $products));
        $adjustedSubtotal = $subtotal - $discount;

        // Select correct delivery strategy **after discount is applied**
        $deliveryStrategy = match (true) {
            $adjustedSubtotal < 50 => new LowOrderDelivery(),
            $adjustedSubtotal < 90 => new MediumOrderDelivery(),
            default => new FreeDelivery(),
        };

        $basketService = new BasketService(
            $this->productRepository,
            $deliveryStrategy,
            $discountStrategy,
            $this->session
        );

        foreach ($products as $productCode) {
            $basketService->addProduct($productCode);
        }

        $this->assertEquals($expectedTotal, $basketService->getTotal(), "Failed on: " . implode(', ', $products));
    }
}
