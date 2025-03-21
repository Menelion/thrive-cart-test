<?php
declare(strict_types=1);
namespace App\Strategy;

use App\Model\Product;

interface DiscountStrategy
{
    /** @param Product[] $products */
    public function applyDiscount(array $products): float;
}
