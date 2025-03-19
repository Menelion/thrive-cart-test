<?php
declare(strict_types=1);
namespace App\Service;

use App\Entity\Charge;
use App\Entity\User;
use App\Repository\ChargeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Value\ChargeEnergy;
use App\Value\ChargeCost;
use App\DataTransfer\UserChargeStatisticsData;
use DateTimeImmutable;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class ChargeService
{
    public function __construct(
        private ChargeRepositoryInterface $chargeRepository,
        private UserRepositoryInterface $userRepository
    )
    {
    }

    public function addCharge(int $userId, int $energyInWh, int $costInCents, bool $isSuccessful): Charge
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new InvalidArgumentException('User not found.');
        }

        $charge = new Charge(
            id: null,
            uuid: Uuid::uuid4(),
            userId: $user->getId(),
            energy: new ChargeEnergy($energyInWh),
            cost: new ChargeCost($costInCents),
            startedAt: new DateTimeImmutable(),
            successful: $isSuccessful
        );

        return $this->chargeRepository->save($charge);
    }

    public function getStatistics(int $userId): UserChargeStatisticsData
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new InvalidArgumentException('User not found.');
        }

        $allCharges = $this->chargeRepository->findByUser($user);
        $charges = $this->chargeRepository->findSuccessfulByUser($user);

        $totalEnergy = 0;
        $totalCost = 0;
        $chargesNumber = count($allCharges);
        $successfulChargesNumber = count($charges);
        
        foreach ($charges as $charge) {
            $totalEnergy += $charge->getEnergy()->toKilowattHours();
            $totalCost += $charge->getCost()->toEuros();
        }

        $averageCost = $chargesNumber > 0 ? $totalCost / $successfulChargesNumber : 0;

        return new UserChargeStatisticsData($chargesNumber, $totalEnergy, $averageCost);
    }
}
