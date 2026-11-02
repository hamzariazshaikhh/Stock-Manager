<?php

declare(strict_types=1);

final class SupplierRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findAllByUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, phone, address, created_at
             FROM suppliers
             WHERE user_id = :user_id
             ORDER BY name ASC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function findByIdForUser(
        int $supplierId,
        int $userId
    ): ?array {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, phone, address
             FROM suppliers
             WHERE id = :supplier_id
             AND user_id = :user_id
             LIMIT 1'
        );

        $statement->execute([
            'supplier_id' => $supplierId,
            'user_id' => $userId,
        ]);

        $supplier = $statement->fetch();

        return $supplier === false ? null : $supplier;
    }

    public function create(
        int $userId,
        string $name,
        ?string $email,
        ?string $phone,
        ?string $address
    ): int {
        $statement = $this->pdo->prepare(
            'INSERT INTO suppliers
                (user_id, name, email, phone, address)
             VALUES
                (:user_id, :name, :email, :phone, :address)'
        );

        $statement->execute([
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $supplierId,
        int $userId,
        string $name,
        ?string $email,
        ?string $phone,
        ?string $address
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE suppliers
             SET name = :name,
                 email = :email,
                 phone = :phone,
                 address = :address
             WHERE id = :supplier_id
             AND user_id = :user_id'
        );

        $statement->execute([
            'supplier_id' => $supplierId,
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $supplierId, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM suppliers
             WHERE id = :supplier_id
             AND user_id = :user_id'
        );

        $statement->execute([
            'supplier_id' => $supplierId,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}