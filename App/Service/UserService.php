<?php
declare(strict_types=1);
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use InvalidArgumentException;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function createUser(string $firstName, string $lastName, string $email): User
    {
        $user = new User(null, $firstName, $lastName, $email);
        return $this->userRepository->save($user);
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new InvalidArgumentException('User not found.');
        }

        $this->userRepository->delete($user);
    }
}
