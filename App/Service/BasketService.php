<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\InvalidProductException;
use App\Model\Basket;
use App\Repository\ProductRepositoryInterface;
use App\Strategy\DeliveryCostStrategy;
use App\Strategy\LowOrderDelivery;
use App\Strategy\MediumOrderDelivery;
use App\Strategy\FreeDelivery;
use App\Strategy\DiscountStrategy;
use SlimSession\Helper as Session;

class BasketService
{
    private Basket $basket;
    private Session $session;
    private ProductRepositoryInterface $productRepository;
    private DiscountStrategy $discountStrategy;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        DiscountStrategy $discountStrategy,
        Session|null $session = null
    )
    {
        $this->productRepository = $productRepository;
        $this->discountStrategy = $discountStrategy;
        $this->session = $session ?? new Session();
        $this->basket = isset($this->session['basket'])
            ? Basket::fromArray($this->session['basket'])
            : new Basket([]);
    }

    /**
     * Adds a product to the basket.
     *
     * @throws InvalidProductException If the product is not found.
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
     * Saves the current basket state to the session.
     */
    public function saveBasketToSession(): void
    {
        $this->session['basket'] = $this->basket->toArray();
    }

    /**
     * Determines the correct delivery strategy based on the subtotal.
     */
    private function getDeliveryStrategy(float $adjustedSubtotal): DeliveryCostStrategy
    {
        return match (true) {
            $adjustedSubtotal < 50 => new LowOrderDelivery(),
            $adjustedSubtotal < 90 => new MediumOrderDelivery(),
            default => new FreeDelivery(),
        };
    }

    /**
     * Gets the total price of all products in the basket.
     *
     * @return float The total price.
     */
    public function getTotal(): float
    {
        $subtotal = round(
            num: array_sum(array_map(fn($item) => $item->getPrice()->toDollars(), $this->basket->getProducts())),
            precision: 2
        );

        $discount = $this->discountStrategy->applyDiscount($this->basket->getProducts());
        $adjustedSubtotal = $subtotal - $discount;

        // Select delivery strategy dynamically based on adjusted subtotal
        $deliveryCostStrategy = $this->getDeliveryStrategy($adjustedSubtotal);
        $deliveryCost = $deliveryCostStrategy->calculateCost($adjustedSubtotal);

        return round($adjustedSubtotal + $deliveryCost, 2);
    }
}
