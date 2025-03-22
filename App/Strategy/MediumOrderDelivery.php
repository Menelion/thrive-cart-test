<?php
declare(strict_types=1);
namespace App\Strategy;

class MediumOrderDelivery implements DeliveryCostStrategy
{
    public function calculateCost(float $subtotal): float {
        return ($subtotal >= 50 && $subtotal < 90) ? 2.95 : 0;
    }
}
