<?php
declare(strict_types=1);
namespace App\Value;

use InvalidArgumentException;

class ChargeEnergy
{
    public function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Charge energy cannot be negative');
        }
    }

    public function toWattHours(): int
    {
        return $this->value;
    }

    public function toKilowattHours(): float
    {
        return $this->value / 1000;
    }
}
