<?php
declare(strict_types=1);
namespace Tests\App\Service;

use App\Model\Basket;
use App\Model\Product;
use App\Repository\InMemoryProductRepository;
use App\Service\BasketService;
use App\Strategy\RedWidgetDiscount;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SlimSession\Helper as Session;

class BasketServiceTest extends TestCase
{
    private InMemoryProductRepository $productRepository;
    private Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        session_start();
        $_SESSION = []; // Reset stored session data
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

    #[DataProvider('basketDataProvider')]
    public function testExampleBaskets(array $products, float $expectedTotal)
    {
        $discountStrategy = new RedWidgetDiscount();
        $basketService = new BasketService(
            $this->productRepository,
            $discountStrategy,
            $this->session
        );

        foreach ($products as $productCode) {
            $basketService->addProduct($productCode);
        }

        $this->assertEquals($expectedTotal, $basketService->getTotal(), "Failed on: " . implode(', ', $products));
    }
}
