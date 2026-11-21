<?php

declare(strict_types=1);

final class DashboardRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function countProductsByUser(int $userId, bool $isOwner = false): int
    {
        if ($isOwner) {
            return (int) $this->pdo->query(
                'SELECT COUNT(*)
                 FROM products
                 WHERE deleted_at IS NULL'
            )->fetchColumn();
        }

        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM products
             WHERE user_id = :user_id
                AND deleted_at IS NULL',
            $userId
        );
    }

    public function countCategoriesByUser(int $userId, bool $isOwner = false): int
    {
        if ($isOwner) {
            return (int) $this->pdo->query(
                'SELECT COUNT(*)
                 FROM categories'
            )->fetchColumn();
        }

        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM categories
             WHERE user_id = :user_id',
            $userId
        );
    }

    public function countSuppliersByUser(int $userId, bool $isOwner = false): int
    {
        if ($isOwner) {
            return (int) $this->pdo->query(
                'SELECT COUNT(*)
                 FROM suppliers'
            )->fetchColumn();
        }

        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM suppliers
             WHERE user_id = :user_id',
            $userId
        );
    }

    public function totalStockByUser(int $userId, bool $isOwner = false): int
    {
        if ($isOwner) {
            $statement = $this->pdo->query(
                'SELECT COALESCE(SUM(quantity), 0)
                 FROM products
                 WHERE deleted_at IS NULL'
            );

            return (int) $statement->fetchColumn();
        }

        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantity), 0)
             FROM products
             WHERE user_id = :user_id
             AND deleted_at IS NULL'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function countLowStockProducts(int $userId, bool $isOwner = false): int
    {
        if ($isOwner) {
            return (int) $this->pdo->query(
                'SELECT COUNT(*)
                 FROM products
                 WHERE deleted_at IS NULL
                 AND quantity <= reorder_level'
            )->fetchColumn();
        }

        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM products
             WHERE user_id = :user_id
                AND deleted_at IS NULL
                AND quantity <= reorder_level',
            $userId
        );
    }

    public function inventoryValue(int $userId, bool $isOwner = false): string
    {
        if ($isOwner) {
            $statement = $this->pdo->query(
                'SELECT COALESCE(SUM(quantity * unit_price), 0)
                 FROM products
                 WHERE deleted_at IS NULL'
            );

            return number_format(
                (float) $statement->fetchColumn(),
                2,
                '.',
                ','
            );
        }

        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantity * unit_price), 0)
             FROM products
             WHERE user_id = :user_id
                AND deleted_at IS NULL'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return number_format(
            (float) $statement->fetchColumn(),
            2,
            '.',
            ','
        );
    }

    public function recentMovements(
        int $userId,
        int $limit = 5,
        bool $isOwner = false
    ): array {
        $limit = max(1, min($limit, 20));

        if ($isOwner) {
            $statement = $this->pdo->prepare(
                "SELECT
                    sm.id,
                    sm.movement_type,
                    sm.quantity_change,
                    sm.quantity_before,
                    sm.quantity_after,
                    sm.note,
                    sm.created_at,
                    p.name AS product_name,
                    p.sku
                 FROM stock_movements sm
                 INNER JOIN products p
                    ON p.id = sm.product_id
                   AND p.deleted_at IS NULL
                 ORDER BY sm.created_at DESC, sm.id DESC
                 LIMIT {$limit}"
            );
            $statement->execute();
            return $statement->fetchAll();
        }

        $statement = $this->pdo->prepare(
            "SELECT
                sm.id,
                sm.movement_type,
                sm.quantity_change,
                sm.quantity_before,
                sm.quantity_after,
                sm.note,
                sm.created_at,
                p.name AS product_name,
                p.sku
             FROM stock_movements sm
             INNER JOIN products p
                ON p.id = sm.product_id
               AND p.user_id = sm.user_id
               AND deleted_at IS NULL
             WHERE sm.user_id = :user_id
             ORDER BY sm.created_at DESC, sm.id DESC
             LIMIT {$limit}"
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    private function fetchCount(
        string $sql,
        int $userId
    ): int {
        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
        ]);

        return (int) $statement->fetchColumn();
    }
}
