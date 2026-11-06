<?php

declare(strict_types=1);

final class StockMovementRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO stock_movements
                (
                    user_id,
                    product_id,
                    movement_type,
                    quantity_change,
                    quantity_before,
                    quantity_after,
                    note
                )
             VALUES
                (
                    :user_id,
                    :product_id,
                    :movement_type,
                    :quantity_change,
                    :quantity_before,
                    :quantity_after,
                    :note
                )'
        );

        $statement->execute([
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'movement_type' => $data['movement_type'],
            'quantity_change' => $data['quantity_change'],
            'quantity_before' => $data['quantity_before'],
            'quantity_after' => $data['quantity_after'],
            'note' => $data['note'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findAllByUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT
                sm.id,
                sm.product_id,
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
             ORDER BY sm.created_at DESC, sm.id DESC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }


public function countByUser(int $userId): int
{
    $statement = $this->pdo->prepare(
        'SELECT COUNT(*)
         FROM stock_movements
         WHERE user_id = :user_id'
    );

    $statement->execute([
        'user_id' => $userId,
    ]);

    return (int) $statement->fetchColumn();
}


public function findPageByUser(
    int $userId,
    int $limit,
    int $offset
): array {
    $limit = max(1, min($limit, 100));
    $offset = max(0, $offset);

    $statement = $this->pdo->prepare(
        "SELECT
            sm.id,
            sm.product_id,
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
         LIMIT {$limit} OFFSET {$offset}"
    );

    $statement->execute([
        'user_id' => $userId,
    ]);

    return $statement->fetchAll();
}

}