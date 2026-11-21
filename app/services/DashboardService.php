<?php

declare(strict_types=1);

final class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $repository
    ) {
    }
public function getSummary(int $userId, bool $isOwner = false): array
{
    return [
        'product_count' => $this->repository->countProductsByUser($userId, $isOwner),
        'category_count' => $this->repository->countCategoriesByUser($userId, $isOwner),
        'supplier_count' => $this->repository->countSuppliersByUser($userId, $isOwner),
        'total_stock' => $this->repository->totalStockByUser($userId, $isOwner),
        'low_stock_count' => $this->repository->countLowStockProducts($userId, $isOwner),
        'inventory_value' => $this->repository->inventoryValue($userId, $isOwner),
        'recent_movements' => $this->repository->recentMovements($userId, 5, $isOwner),
    ];
}


}