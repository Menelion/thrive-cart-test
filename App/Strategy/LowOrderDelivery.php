<?php
namespace App\Strategy;

class LowOrderDelivery implements DeliveryCostStrategy
{
    public function calculateCost(float $subtotal): float
    {
        return $subtotal < 50 ? 4.95 : 0;
    }
}
