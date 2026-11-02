<?php

declare(strict_types=1);

final class ProductRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findAllByUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT
                p.id,
                p.name,
                p.sku,
                p.description,
                p.quantity,
                p.reorder_level,
                p.unit_price,
                p.category_id,
                p.supplier_id,
                p.created_at,
                p.updated_at,
                c.name AS category_name,
                s.name AS supplier_name
             FROM products p
             LEFT JOIN categories c
                ON c.id = p.category_id
               AND c.user_id = p.user_id
             LEFT JOIN suppliers s
                ON s.id = p.supplier_id
               AND s.user_id = p.user_id
             WHERE p.user_id = :user_id
             ORDER BY p.name ASC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function findByIdForUser(
        int $productId,
        int $userId
    ): ?array {
        $statement = $this->pdo->prepare(
            'SELECT
                id,
                user_id,
                category_id,
                supplier_id,
                name,
                sku,
                description,
                quantity,
                reorder_level,
                unit_price,
                created_at,
                updated_at
             FROM products
             WHERE id = :product_id
               AND user_id = :user_id
             LIMIT 1'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
        ]);

        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    public function findByIdForUpdate(
        int $productId,
        int $userId
    ): ?array {
        $statement = $this->pdo->prepare(
            'SELECT id, quantity
             FROM products
             WHERE id = :product_id
               AND user_id = :user_id
             LIMIT 1
             FOR UPDATE'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
        ]);

        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    public function skuExistsForUser(
        string $sku,
        int $userId,
        ?int $ignoreProductId = null
    ): bool {
        $sql = '
            SELECT id
            FROM products
            WHERE user_id = :user_id
              AND sku = :sku
        ';

        $parameters = [
            'user_id' => $userId,
            'sku' => $sku,
        ];

        if ($ignoreProductId !== null) {
            $sql .= ' AND id != :ignore_product_id';

            $parameters['ignore_product_id'] = $ignoreProductId;
        }

        $sql .= ' LIMIT 1';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetch() !== false;
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO products
                (
                    user_id,
                    category_id,
                    supplier_id,
                    name,
                    sku,
                    description,
                    quantity,
                    reorder_level,
                    unit_price
                )
             VALUES
                (
                    :user_id,
                    :category_id,
                    :supplier_id,
                    :name,
                    :sku,
                    :description,
                    :quantity,
                    :reorder_level,
                    :unit_price
                )'
        );

        $statement->execute([
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'reorder_level' => $data['reorder_level'],
            'unit_price' => $data['unit_price'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $productId,
        int $userId,
        array $data
    ): bool {
        /*
         * Quantity is intentionally not updated here.
         * Inventory quantity must be changed through StockService.
         */
        $statement = $this->pdo->prepare(
            'UPDATE products
             SET category_id = :category_id,
                 supplier_id = :supplier_id,
                 name = :name,
                 sku = :sku,
                 description = :description,
                 reorder_level = :reorder_level,
                 unit_price = :unit_price
             WHERE id = :product_id
               AND user_id = :user_id'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'],
            'reorder_level' => $data['reorder_level'],
            'unit_price' => $data['unit_price'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function updateQuantity(
        int $productId,
        int $userId,
        int $quantity
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE products
             SET quantity = :quantity
             WHERE id = :product_id
               AND user_id = :user_id'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'quantity' => $quantity,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $productId, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM products
             WHERE id = :product_id
               AND user_id = :user_id'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}