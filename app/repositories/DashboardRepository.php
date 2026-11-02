<?php

declare(strict_types=1);

final class DashboardRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function countProductsByUser(int $userId): int
    {
        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM products
             WHERE user_id = :user_id',
            $userId
        );
    }

    public function countCategoriesByUser(int $userId): int
    {
        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM categories
             WHERE user_id = :user_id',
            $userId
        );
    }

    public function countSuppliersByUser(int $userId): int
    {
        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM suppliers
             WHERE user_id = :user_id',
            $userId
        );
    }

    public function totalStockByUser(int $userId): int
    {
        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantity), 0)
             FROM products
             WHERE user_id = :user_id'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function countLowStockProducts(int $userId): int
    {
        return $this->fetchCount(
            'SELECT COUNT(*)
             FROM products
             WHERE user_id = :user_id
             AND quantity <= reorder_level',
            $userId
        );
    }

    public function inventoryValue(int $userId): string
    {
        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantity * unit_price), 0)
             FROM products
             WHERE user_id = :user_id'
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
        int $limit = 5
    ): array {
        $limit = max(1, min($limit, 20));

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