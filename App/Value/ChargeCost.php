<?php
declare(strict_types=1);
namespace App\Value;

use InvalidArgumentException;

class ChargeCost
{
    public function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Charge cost cannot be negative.');
        }
    }

    public function toCents(): int
    {
        return $this->value;
    }

    public function toEuros(): float
    {
        return $this->value / 100;
    }
}
