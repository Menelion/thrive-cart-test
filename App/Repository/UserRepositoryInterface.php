<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Charge;
use App\Entity\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    /** @return User[] */
    public function findAll(): array;
    public function save(User $user): User;
    public function delete(User $user): void;
}
