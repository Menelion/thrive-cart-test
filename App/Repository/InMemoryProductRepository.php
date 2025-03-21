<?php
declare(strict_types=1);
namespace App\Repository;

use App\Model\Product;
use App\Value\ProductPrice;

class InMemoryProductRepository implements ProductRepositoryInterface
{
    /** @var Product[] $products */
    private array $products = [];

    public function __construct()
    {
        $this->products = [
            new Product('R01', 'Red Widget', new ProductPrice(3295)),
            new Product('G01', 'Green Widget', new ProductPrice(2495)),
            new Product('B01', 'Blue Widget', new ProductPrice(795)),
            ];
    }

    public function findByCode(string $code, bool $caseSensitive = true): ?Product
    {
        if (!$caseSensitive) {
            return array_reduce(
                $this->products,
                fn(?Product $product, Product $item) => $product
                    ?? (strtolower($item->getCode()) === strtolower($code) ? $item : null)
            );
        }

        return array_reduce(
            $this->products,
            fn(?Product $product, Product $item) => $product ?? ($item->getCode() === $code ? $item : null)
        );
    }
}
