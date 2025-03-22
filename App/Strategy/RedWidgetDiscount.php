<?php
declare(strict_types=1);
namespace App\Strategy;

use App\Model\Product;

class RedWidgetDiscount implements DiscountStrategy
{
    /** @param Product[] $products */
    public function applyDiscount(array $products): float
    {
        $redWidgets = array_values(array_filter($products, fn($item) => $item->code === 'R01')); // Reindex array
        $discount = 0;

        if (count($redWidgets) >= 2) {
            $discount = round($redWidgets[1]->getPrice()->toDollars() * 0.5, 2); // Convert cents to dollars first
        }

        return $discount;
    }
        }
