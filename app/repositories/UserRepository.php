<?php

declare(strict_types=1);

final class UserRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, email, role, created_at
             FROM users
             ORDER BY name ASC'
        );

        return $statement->fetchAll();
        
    }

    public function findById(int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, role
             FROM users
             WHERE id = :user_id
             LIMIT 1'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function updateRole(int $userId, string $role): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE users
             SET role = :role
             WHERE id = :user_id'
        );

        $statement->execute([
            'user_id' => $userId,
            'role' => $role,
        ]);

        return $statement->rowCount() > 0;
    }

    public function countOwners(): int
    {
        $statement = $this->pdo->query(
            "SELECT COUNT(*)
             FROM users
             WHERE role = 'owner'"
        );

        return (int) $statement->fetchColumn();
    }
}