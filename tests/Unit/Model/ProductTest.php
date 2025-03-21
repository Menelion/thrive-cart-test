<?php
declare(strict_types=1);
namespace Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use App\Model\Product;

class ProductTest extends TestCase
{
    public function testFromArrayToArray()
    {
        $data = ['code' => 'R01', 'name' => 'Red Widget', 'price' => 32.95];

        $product = Product::fromArray($data);

        $this->assertEquals($data, $product->toArray());
        $this->assertEquals(3295, $product->getPrice()->toCents());
    }
}
