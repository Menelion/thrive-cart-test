<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Charge;
use App\Entity\User;

interface ChargeRepositoryInterface
{
    public function findById(int $id): ?Charge;

    /** @return Charge[] */
    public function findByUser(User $user): array;

    /** return Charge[]  */
    public function findSuccessfulByUser(User $user): array;
    public function save(Charge $charge): Charge;
}
