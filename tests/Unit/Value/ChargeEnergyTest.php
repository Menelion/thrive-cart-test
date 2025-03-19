<?php
declare(strict_types=1);
namespace Tests\Unit;

use App\Value\ChargeEnergy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChargeEnergyTest extends TestCase
{
    public function testEnergyInitialization(): void
    {
        $energy = new ChargeEnergy(1000);
        $this->assertEquals(1000, $energy->toWattHours());
        $this->assertEquals(1.0, $energy->toKilowattHours());
    }

    public function testNegativeEnergyThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Charge energy cannot be negative');
        new ChargeEnergy(-100);
    }
}
