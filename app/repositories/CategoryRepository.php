<?php

declare(strict_types=1);

final class CategoryRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findAllByUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, created_at
             FROM categories
             WHERE user_id = :user_id
             ORDER BY name ASC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function findByIdForUser(
        int $categoryId,
        int $userId
    ): ?array {
        $statement = $this->pdo->prepare(
            'SELECT id, name, created_at
             FROM categories
             WHERE id = :category_id
             AND user_id = :user_id
             LIMIT 1'
        );

        $statement->execute([
            'category_id' => $categoryId,
            'user_id' => $userId,
        ]);

        $category = $statement->fetch();

        return $category === false ? null : $category;
    }

    public function existsByNameForUser(
        string $name,
        int $userId,
        ?int $ignoreCategoryId = null
    ): bool {
        $sql = '
            SELECT id
            FROM categories
            WHERE user_id = :user_id
            AND name = :name
        ';

        $parameters = [
            'user_id' => $userId,
            'name' => $name,
        ];

        if ($ignoreCategoryId !== null) {
            $sql .= ' AND id != :ignore_category_id';

            $parameters['ignore_category_id'] = $ignoreCategoryId;
        }

        $sql .= ' LIMIT 1';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetch() !== false;
    }

    public function create(string $name, int $userId): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO categories
                (user_id, name)
             VALUES
                (:user_id, :name)'
        );

        $statement->execute([
            'user_id' => $userId,
            'name' => $name,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $categoryId,
        int $userId,
        string $name
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE categories
             SET name = :name
             WHERE id = :category_id
             AND user_id = :user_id'
        );

        $statement->execute([
            'category_id' => $categoryId,
            'user_id' => $userId,
            'name' => $name,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $categoryId, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM categories
             WHERE id = :category_id
             AND user_id = :user_id'
        );

        $statement->execute([
            'category_id' => $categoryId,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}