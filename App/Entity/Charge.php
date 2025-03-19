<?php
declare(strict_types=1);
namespace App\Entity;

use App\Value\ChargeCost;
use App\Value\ChargeEnergy;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

class Charge
{
    public function __construct(
        private ?int $id,
        private UuidInterface $uuid,
        private int $userId,
        private ChargeEnergy $energy,
        private ChargeCost $cost,
        private DateTimeImmutable $startedAt,
        private bool $successful,
    )
    {
    }

    /** @param mixed[] $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            uuid: Uuid::fromString($data['uuid']),
            userId: $data['user_id'],
            energy: new ChargeEnergy($data['energy']),
            cost: new ChargeCost($data['cost']),
            startedAt: (new DateTimeImmutable())->setTimestamp($data['started_at']),
            successful: (bool) $data['successful'],
        );
    }

    /** return mixed[] */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid->toString(),
            'userId' => $this->userId,
            'energy' => $this->energy->toKilowattHours(),
            'cost' => $this->cost->toEuros(),
            'startedAt' => $this->startedAt->getTimestamp(),
            'successful' => $this->successful,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEnergy(): ChargeEnergy
    {
        return $this->energy;
    }

    public function getCost(): ChargeCost
    {
        return $this->cost;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }
}
