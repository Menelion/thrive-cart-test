<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\InvalidProductException;
use App\Model\Basket;
use App\Repository\ProductRepositoryInterface;
use App\Strategy\DeliveryCostStrategy;
use App\Strategy\DiscountStrategy;
use SlimSession\Helper as Session;

class BasketService
{
    private Basket $basket;
    private Session $session;
    private ProductRepositoryInterface $productRepository;
    private DeliveryCostStrategy $deliveryCostStrategy;
    private DiscountStrategy $discountStrategy;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        DeliveryCostStrategy $deliveryCostStrategy,
        DiscountStrategy $discountStrategy,
        Session|null $session = null
    )
    {
        $this->productRepository = $productRepository;
        $this->deliveryCostStrategy = $deliveryCostStrategy;
        $this->discountStrategy = $discountStrategy;
        $this->session = $session ?? new Session();
        $this->basket = isset($this->session['basket'])
            ? Basket::fromArray($this->session['basket'])
            : new Basket([]);
    }

    /**
     * Adds a product to the basket
     *
     * @param string $code The product code
     * @return Basket The updated basket
     * @throws InvalidProductException If the product is not found
     */
    public function addProduct(string $code): Basket
    {
        $product = $this->productRepository->findByCode(code: $code, caseSensitive: false);

        if (!$product) {
            throw new InvalidProductException(sprintf('Product with code %s not found', $code));
        }

        $this->basket->add($product);
        $this->saveBasketToSession();

        return $this->basket;
    }

    /**
     * Saves the current basket state to the session
     *
     * @return void
     */
    public function saveBasketToSession(): void
    {
        $this->session['basket'] = $this->basket->toArray();
    }

    /**
     * Gets the total price of all products in the basket
     *
     * @return float The total price
     */
    public function getTotal(): float
    {
        $subtotal = round(
            num: array_sum(array_map(fn($item) => $item->getPrice()->toDollars(), $this->basket->getProducts())),
            precision: 2
        );

        $discount = $this->discountStrategy->applyDiscount($this->basket->getProducts());
        $deliveryCost = $this->deliveryCostStrategy->calculateCost($subtotal - $discount);

        return round($subtotal - $discount + $deliveryCost, 2);
    }
}
