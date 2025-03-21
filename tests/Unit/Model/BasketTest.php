<?php
use PHPUnit\Framework\TestCase;
use App\Model\Basket;
use App\Model\Product;

class BasketTest extends TestCase
{
    public function testAddProduct()
    {
        $basket = new Basket([]);
        $product = Product::fromArray(['code' => 'R01', 'name' => 'Red Widget', 'price' => 32.95]);

        $basket->add($product);

        $this->assertCount(1, $basket->getProducts());
        $this->assertSame($product, $basket->getProducts()[0]);
        $this->assertSame('R01', $basket->getProducts()[0]->getCode());
    }

    public function testToArrayFromArray()
    {
        $basket = new Basket([]);
        $product1 = Product::fromArray(['code' => 'R01', 'name' => 'Red Widget', 'price' => 32.95]);
        $product2 = Product::fromArray(['code' => 'G01', 'name' => 'Green Widget', 'price' => 24.95]);
        $basket = $basket->add($product1)->add($product2);

        $arrayData = $basket->toArray();
        $newBasket = Basket::fromArray($arrayData);
        $this->assertEquals($basket->toArray(), $newBasket->toArray());
    }
}
