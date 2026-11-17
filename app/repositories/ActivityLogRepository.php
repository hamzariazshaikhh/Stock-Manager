<?php
declare(strict_types=1);

final class ActivityLogRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function log(int $userId, string $action, string $description): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $statement = $this->pdo->prepare(
            'INSERT INTO activity_logs (user_id, action, description, ip_address)
             VALUES (:user_id, :action, :description, :ip_address)'
        );

        $statement->execute([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip,
        ]);
    }

    public function findPage(int $limit, int $offset): array
    {
        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);

        $statement = $this->pdo->prepare(
            'SELECT 
                al.id,
                al.action,
                al.description,
                al.ip_address,
                al.created_at,
                u.email AS user_email,
                u.role AS user_role
             FROM activity_logs al
             INNER JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC, al.id DESC
             LIMIT :limit OFFSET :offset'
        );

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function countAll(): int
    {
        $statement = $this->pdo->query('SELECT COUNT(*) FROM activity_logs');
        return (int) $statement->fetchColumn();
    }
}
