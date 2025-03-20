<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\InvalidProductException;
use App\Model\Basket;
use App\Repository\ProductRepositoryInterface;

class BasketService
{
    private Basket $basket;

    public function __construct(private ProductRepositoryInterface $productRepository)
    {
        session_start();
        $this->basket = isset($_SESSION['basket'])
            ? Basket::fromArray($_SESSION['basket'])
            : new Basket([]);
    }

    public function __destruct() {
        $_SESSION['basket'] = $this->basket->toArray();
    }

    public function addProduct(string $code): void
    {
        $product = $this->productRepository->findByCode($code);

        if (!$product) {
            throw new InvalidProductException(sprintf('Product with code %s not found', $code));
        }

        $this->basket->add($product);
    }

    public function getTotal(): float
    {
        return array_sum(array_map(fn($item) => $item->getPrice()->toCents(), $this->basket->getProducts())) / 100;
    }
}
