<?php
declare(strict_types=1);
namespace App\DataTransfer;

class UserChargeStatisticsData
{
    public function __construct(
        private int $chargesNumber,
        private float $totalEnergy,
        private float $averageCost,
    )
    {
    }

    /** return mixed[] */
    public function toArray(): array
    {
        return [
            'chargesNumber' => $this->chargesNumber,
            'totalEnergy' => $this->totalEnergy,
            'averageCost' => $this->averageCost,
        ];
    }

    public function getChargesNumber(): int
    {
        return $this->chargesNumber;
    }

    public function getTotalEnergy(): float
    {
        return $this->totalEnergy;
    }

    public function getAverageCost(): float
    {
        return $this->averageCost;
    }
}
