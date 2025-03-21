<?php
declare(strict_types=1);
namespace Tests\App\Service;

use App\Model\Basket;
use App\Model\Product;
use App\Repository\InMemoryProductRepository;
use App\Service\BasketService;
use App\Strategy\DeliveryCostStrategy;
use App\Strategy\DiscountStrategy;
use PHPUnit\Framework\TestCase;
use PhpUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
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

    public function testAddProduct(): void
    {
        $product = Product::fromArray(['code' => 'R01', 'name' => 'Red Widget', 'price' => 32.95]);
        $basketService = new BasketService($this->productRepository, $this->session);

        $result = $basketService->addProduct('R01');

        $this->assertInstanceOf(Basket::class, $result);
        $this->assertCount(1, $result->getProducts());

        $this->assertArrayHasKey('basket', $this->session);
        $this->assertIsArray($this->session['basket']);
        $this->assertArrayHasKey('products', $this->session['basket']);
    }

    public function testSaveBasketToSession(): void
    {
        $product = Product::fromArray(['code' => 'R01', 'name' => 'Red Widget', 'price' => 32.95]);
        $basketService = new BasketService($this->productRepository, $this->session);

        $basketReflection = new ReflectionProperty($basketService, 'basket');
        $basketReflection->setAccessible(true);
        $basket = new Basket([$product]);
        $basketReflection->setValue($basketService, $basket);

        $basketService->saveBasketToSession();

        $this->assertArrayHasKey('basket', $this->session);
        $this->assertIsArray($this->session['basket']);
        $this->assertArrayHasKey('products', $this->session['basket']);
    }

    public function testGetTotal() {
        $service = new BasketService($this->productRepository, $this->session);
        $service->addProduct('R01');
        $service->addProduct('G01');
        $this->assertEquals(57.90, $service->getTotal());
    }
}
