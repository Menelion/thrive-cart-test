<?php
declare(strict_types=1);
namespace Tests\Unit\Service;

use App\Repository\InMemoryProductRepository;
use PHPUnit\Framework\TestCase;
use App\Service\BasketService;
use App\Repository\InMemoryProductRepositoryProductRepository;
use SlimSession\Helper as Session;
use Slim\App;

class BasketServiceTest extends TestCase
{
    private App $app;
    private Session $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = createApp();
        $this->session = new Session();
        var_export($this->session->get('basket'));
    }

    public function testAddProductUpdatesBasket() {
        $service = new BasketService(new InMemoryProductRepository());
        $service->addProduct('R01');
        $session = new Session();

        $this->assertEquals(1, count($session->basket));
        $this->assertEquals('R01', $session->basket[0]['code']);
    }

    public function testGetTotal()
    {
        $service = new BasketService(new InMemoryProductRepository());
        $service->addProduct('R01');
        $service->addProduct('G01');

        $this->assertEquals(57.90, $service->getTotal());
    }
}
