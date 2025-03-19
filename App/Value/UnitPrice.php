<?php
declare(strict_types=1);
namespace App\Value;

use InvalidArgumentException;

class UnitPrice
{
    private const DEFAULT_PRICE = 40;
    private static ?self $defaultInstance = null;

    private function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Price per kWh cannot be negative');
        }
    }

    public static function create(int $pricePerKilowattHour): self
    {
        return new self($pricePerKilowattHour);
    }

    public static function createDefault(): self
    {
        if (self::$defaultInstance === null) {
            self::$defaultInstance = new self(self::DEFAULT_PRICE);
        }

        return self::$defaultInstance;
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
