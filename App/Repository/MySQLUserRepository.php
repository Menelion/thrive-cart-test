<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\User;
use App\Exception\UserException;
use ParagonIE\EasyDB\EasyDB;
use Throwable;

class MySQLUserRepository implements UserRepositoryInterface
{
    public function __construct(private EasyDB $db)
    {
    }

    public function findById(int $id): ?User
    {
        try {
            $row = $this->db->row('SELECT * FROM users WHERE id = ?', $id);
        } catch (Throwable $e) {
            throw new UserException(sprintf('Unable to select user: %s', $e->getMessage()), $e);
        }

        return $row ? User::fromArray($row) : null;
    }

    /** @return User[] */
    public function findAll(): array
    {
        try {
            $results = $this->db->run('SELECT * FROM users');
        } catch (Throwable $e) {
            throw new UserException(sprintf('Unable to select users: %s', $e->getMessage()), $e);
        }

        return array_map(fn($row): User => User::fromArray($row), $results);
    }

    public function save(User $user): User
    {
        if ($user->getId() !== null) {
            try {
                $this->db->update(
                    'users',
                    [
                        'first_name' => $user->getFirstName(),
                        'last_name' => $user->getLastName(),
                        'email' => $user->getEmail(),
                    ],
                    ['id' => $user->getId()]
                );
            } catch (Throwable $e) {
                throw new UserException(sprintf('Unable to update user: %s', $e->getMessage()), $e);
            }
        } else {
            try {
                $this->db->insert(
                    'users',
                    [
                        'first_name' => $user->getFirstName(),
                        'last_name' => $user->getLastName(),
                        'email' => $user->getEmail(),
                    ]
                );

                $user->setId((int) $this->db->lastInsertId());
            } catch (Throwable $e) {
                throw new UserException(sprintf('Unable to add new user: %s', $e->getMessage()), $e);
            }
        }

        return $user;
    }

    public function delete(User $user): void
    {
        try {
            $this->db->delete('users', ['id' => $user->getId()]);
        } catch (Throwable $e) {
            throw new UserException(sprintf('Unable to delete user: %s', $e->getMessage()), $e);
        }
    }
}
