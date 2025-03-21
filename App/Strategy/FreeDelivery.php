<?php
declare(strict_types=1);
namespace App\Strategy;

class FreeDelivery implements DeliveryCostStrategy
{
    public function calculateCost(float $subtotal): float
    {
        return $subtotal >= 90 ? 0 : 4.95; // Fallback to highest cost if no match
    }
}
