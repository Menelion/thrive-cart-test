<?php
declare(strict_types=1);
namespace App\Strategy;

use App\Model\Product;

class RedWidgetDiscount implements DiscountStrategy
{
    /** @param Product[] $products */
    public function applyDiscount(array $products): float
    {
        $redWidgets = array_filter($products, fn($item) => $item->code === 'R01');
        $discount = 0;

        if (count($redWidgets) >= 2) {
            $discount = round(num: ($redWidgets[1]->getPrice()->toCents() * 0.5), precision: 2); // Apply half-price to second one
        }

        return $discount;
    }
}
