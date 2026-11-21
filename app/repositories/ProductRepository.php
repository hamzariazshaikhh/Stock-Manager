<?php

declare(strict_types=1);

final class ProductRepository
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
                LEFT JOIN users u
                    ON u.id = p.user_id
                 WHERE p.deleted_at IS NULL
                 ORDER BY p.name ASC, p.id ASC'
            );

            $statement->execute();

            return $statement->fetchAll();
        }
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
               AND p.deleted_at IS NULL
             ORDER BY p.name ASC, p.id ASC'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function countByUser(int $userId, bool $isOwner = false): int
    {
        if ($isOwner) {
            $statement = $this->pdo->query(
                'SELECT COUNT(*)
                 FROM products
                 WHERE deleted_at IS NULL'
            );

            return (int) $statement->fetchColumn();
        }
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM products
             WHERE user_id = :user_id
               AND deleted_at IS NULL'
        );

        $statement->execute([
            'user_id' => $userId,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function findPageByUser(
        int $userId,
        int $limit,
        int $offset,
        bool $isOwner = false
    ): array {
        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);

        if ($isOwner) {
            $statement = $this->pdo->prepare(
                "SELECT
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
                    s.name AS supplier_name,
                    u.email AS user_email, u.name AS user_namex
                 FROM products p
                 LEFT JOIN categories c
                    ON c.id = p.category_id
                   AND c.user_id = p.user_id
                 LEFT JOIN suppliers s
                    ON s.id = p.supplier_id
                   AND s.user_id = p.user_id
                LEFT JOIN users u
                    ON u.id = p.user_id
                 WHERE p.deleted_at IS NULL
                 ORDER BY p.name ASC, p.id ASC
                 LIMIT :limit OFFSET :offset"
            );
            /*
            $statement->execute([
                'limit' => $limit,
                'offset' => $offset,
            ]);
*/
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->execute();

            return $statement->fetchAll();
        }
        $statement = $this->pdo->prepare(
            "SELECT
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
               AND p.deleted_at IS NULL
             ORDER BY p.name ASC, p.id ASC
             LIMIT :limit OFFSET :offset"
        );

        $statement->execute([
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return $statement->fetchAll();
    }


public function findByIdForUser(
    int $productId,
    int $userId,
    bool $isOwner = false
): ?array {
    if ($isOwner) {
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
                s.name AS supplier_name,
                u.email AS user_email,
                u.name AS user_name
             FROM products p
             LEFT JOIN categories c
                ON c.id = p.category_id
               AND c.user_id = p.user_id
             LEFT JOIN suppliers s
                ON s.id = p.supplier_id
               AND s.user_id = p.user_id
             LEFT JOIN users u
                ON u.id = p.user_id
             WHERE p.id = :product_id
               AND p.deleted_at IS NULL
             LIMIT 1'
        );

        $statement->execute([
            'product_id' => $productId,
        ]);
    } else {
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
             WHERE p.id = :product_id
               AND p.user_id = :user_id
               AND p.deleted_at IS NULL
             LIMIT 1'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
        ]);
    }

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
               AND deleted_at IS NULL
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
        array $data,
        bool $isOwner = false
    ): bool {
        if ($isOwner) {
            $sql = 'UPDATE products
                    SET category_id = :category_id,
                        supplier_id = :supplier_id,
                        name = :name,
                        sku = :sku,
                        description = :description,
                        reorder_level = :reorder_level,
                        unit_price = :unit_price
                    WHERE id = :product_id
                      AND deleted_at IS NULL';
            $params = [
                'product_id' => $productId,
                'category_id' => $data['category_id'],
                'supplier_id' => $data['supplier_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'description' => $data['description'],
                'reorder_level' => $data['reorder_level'],
                'unit_price' => $data['unit_price'],
            ];
        } else {
            $sql = 'UPDATE products
                    SET category_id = :category_id,
                        supplier_id = :supplier_id,
                        name = :name,
                        sku = :sku,
                        description = :description,
                        reorder_level = :reorder_level,
                        unit_price = :unit_price
                    WHERE id = :product_id
                      AND user_id = :user_id
                      AND deleted_at IS NULL';
            $params = [
                'product_id' => $productId,
                'user_id' => $userId,
                'category_id' => $data['category_id'],
                'supplier_id' => $data['supplier_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'description' => $data['description'],
                'reorder_level' => $data['reorder_level'],
                'unit_price' => $data['unit_price'],
            ];
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
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
               AND user_id = :user_id
               AND deleted_at IS NULL'
        );

        $statement->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'quantity' => $quantity,
        ]);

        return $statement->rowCount() > 0;
    }

      public function delete(int $productId, int $userId, bool $isOwner = false): bool
    {
        if ($isOwner) {
            $sql = 'UPDATE products
                    SET deleted_at = CURRENT_TIMESTAMP
                    WHERE id = :product_id
                      AND deleted_at IS NULL';
            $params = ['product_id' => $productId];
        } else {
            $sql = 'UPDATE products
                    SET deleted_at = CURRENT_TIMESTAMP
                    WHERE id = :product_id
                      AND user_id = :user_id
                      AND deleted_at IS NULL';
            $params = [
                'product_id' => $productId,
                'user_id' => $userId,
            ];
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount() > 0;
    }
}