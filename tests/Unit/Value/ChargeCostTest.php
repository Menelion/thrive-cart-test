<?php
declare(strict_types=1);
namespace Tests\Unit;

use App\Value\ChargeCost;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChargeCostTest extends TestCase
{
    public function testCostInitialization(): void
    {
        $cost = new ChargeCost(2500);
        $this->assertEquals(2500, $cost->toCents());
        $this->assertEquals(25.00, $cost->toEuros());
    }

    public function testNegativeCostThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Charge cost cannot be negative');
        new ChargeCost(-100);
    }
}
