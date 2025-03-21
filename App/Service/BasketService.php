<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\InvalidProductException;
use App\Model\Basket;
use App\Repository\ProductRepositoryInterface;
use SlimSession\Helper as Session;

class BasketService
{
    private Basket $basket;

    public function __construct(private ProductRepositoryInterface $productRepository)
    {
        $session = new Session();
        $this->basket = isset($session['basket'])
            ? Basket::fromArray($session->basket)
            : new Basket([]);
    }

    public function __destruct()
    {
        $session = new Session();
        $session->basket = $this->basket->toArray();
    }

    public function addProduct(string $code): Basket
    {
        $product = $this->productRepository->findByCode($code);

        if (!$product) {
            throw new InvalidProductException(sprintf('Product with code %s not found', $code));
        }

        $this->basket->add($product);

        return $this->basket;
    }

    public function getTotal(): float
    {
        return round(
            num: array_sum(array_map(fn($item) => $item->getPrice()->toCents(), $this->basket->getProducts())) / 100,
            precision: 2
        );
    }
}
