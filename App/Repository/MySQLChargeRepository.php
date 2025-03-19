<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\User;
use App\Entity\Charge;
use App\Exception\ChargeException;
use ParagonIE\EasyDB\EasyDB;
use Throwable;

class MySQLChargeRepository implements ChargeRepositoryInterface
{
    public function __construct(private EasyDB $db)
    {
    }

    public function findById(int $id): ?Charge
    {
        try {
            $row = $this->db->row('SELECT * FROM charges WHERE id = ?', $id);
        } catch (Throwable $e) {
            throw new ChargeException(sprintf('Unable to select charge: %s', $e->getMessage()), $e);
        }

        return $row ? Charge::fromArray($row) : null;
    }

    /** @return Charge[] */
    public function findByUser(User $user): array
    {
        try {
            $results = $this->db->run(
                'SELECT * FROM charges WHERE user_id = ?',
                $user->getId()
            );
        } catch (Throwable $e) {
            throw new ChargeException(sprintf('Unable to find charges of user %d: %s', $user->getId(), $e->getMessage()), $e);
        }

        return array_map(fn($row): Charge => Charge::fromArray($row), $results);
    }

    /** @return Charge[] */
    public function findSuccessfulByUser(User $user): array
    {
        try {
            $results = $this->db->run(
                'SELECT * FROM charges WHERE user_id = ? AND successful = 1',
                $user->getId()
            );
        } catch (Throwable $e) {
            throw new ChargeException(sprintf('Unable to find successful charges of user %d: %s', $user->getId(), $e->getMessage()), $e);
        }

        return array_map(fn($row): Charge => Charge::fromArray($row), $results);
    }

    public function save(Charge $charge): Charge
    {
        try {
            $this->db->insert(
                'charges',
                [
                    'uuid' => $charge->getUuid()->toString(),
                    'user_id' => $charge->getUserId(),
                    'energy' => $charge->getEnergy()->toWattHours(),
                    'cost' => $charge->getCost()->toCents(),
                    'started_at' => $charge->getStartedAt()->getTimestamp(),
                    'successful' => $charge->isSuccessful(),
                ]
            );
        } catch (Throwable $e) {
            throw new ChargeException(sprintf('Unable to add new charge: %s', $e->getMessage()), $e);
        }

        $charge->setId((int) $this->db->lastInsertId());

        return $charge;
    }
}
