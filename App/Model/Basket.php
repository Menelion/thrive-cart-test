<?php
declare(strict_types=1);
namespace App\Model;

class Basket
{
 /** @param Product[] $products */
    public function __construct(public array $products)
    {
    }

    public function add(Product $product): self
    {
        $this->products[] = $product;

        return $this;
    }

    /** @param mixed[] $data */
    public static function fromArray(array $data): self
    {
        return new self(array_map(fn($product) => Product::fromArray($product), $data['products']));
    }

    /** return mixed[] */
    public function toArray(): array
    {
        return [
            'products' => array_map(fn($product) => $product->toArray(), $this->products),
        ];
    }

    /** return Product[] */
    public function getProducts(): array
    {
        return $this->products;
    }
}
