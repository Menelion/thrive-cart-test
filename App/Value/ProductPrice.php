<?php
declare(strict_types=1);
namespace App\Value;

use InvalidArgumentException;

class ProductPrice
{
    public function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Product price cannot be negative.');
        }
    }

    public function toCents(): int
    {
        return $this->value;
    }

    public function toDollars(): float
    {
        return round($this->value / 100, 2);
    }
}
