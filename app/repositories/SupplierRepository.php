<?php

declare(strict_types=1);

final class SupplierRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function findAllByUser(int $userId, bool $isOwner = false): array
    {
        if ($isOwner) {
            $statement = $this->pdo->query(
                'SELECT s.id, s.name, s.email, s.phone, s.address, s.created_at, u.email AS user_email, u.name AS user_name
                 FROM suppliers s
                 INNER JOIN users u ON u.id = s.user_id
                 ORDER BY s.name ASC'
            );

            $statement->execute();
            return $statement->fetchAll();
        }

        $statement = $this->pdo->prepare(
            'SELECT s.id, s.name, s.email, s.phone, s.address, s.created_at, u.email AS user_email, u.name AS user_name
             FROM suppliers s
             INNER JOIN users u ON u.id = s.user_id
             WHERE s.user_id = :user_id
             ORDER BY s.name ASC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function findByIdForUser(
        int $supplierId,
        int $userId,
        bool $isOwner = false
    ): ?array {

        if ($isOwner) {
            $statement = $this->pdo->prepare(
                'SELECT id, name, email, phone, address
                 FROM suppliers
                 WHERE id = :supplier_id
                 LIMIT 1'
            );

            $statement->execute([
                'supplier_id' => $supplierId,
            ]);

            $supplier = $statement->fetch();

            return $supplier === false ? null : $supplier;
        }
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
        ?string $address,
        bool $isOwner = false
    ): bool {

        if ($isOwner) {
            $statement = $this->pdo->prepare(
                'UPDATE suppliers
                 SET name = :name,
                     email = :email,
                     phone = :phone,
                     address = :address
                 WHERE id = :supplier_id'
            );

            $statement->execute([
                'supplier_id' => $supplierId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ]);

            return $statement->rowCount() > 0;
        }
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

    public function delete(int $supplierId, int $userId, bool $isOwner = false): bool
    {
        if ($isOwner) {
            $statement = $this->pdo->prepare(
                'DELETE FROM suppliers
                 WHERE id = :supplier_id'
            );

            $statement->execute([
                'supplier_id' => $supplierId,
            ]);

            return $statement->rowCount() > 0;
        }
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