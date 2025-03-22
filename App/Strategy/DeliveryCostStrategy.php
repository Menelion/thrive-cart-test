<?php
declare(strict_types=1);
namespace App\Strategy;

interface DeliveryCostStrategy
{
    public function calculateCost(float $subtotal): float;
}
