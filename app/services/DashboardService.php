<?php

declare(strict_types=1);

final class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $repository
    ) {
    }

    public function getSummary(int $userId): array
    {
        return [
            'product_count' => $this->repository
                ->countProductsByUser($userId),

            'category_count' => $this->repository
                ->countCategoriesByUser($userId),

            'supplier_count' => $this->repository
                ->countSuppliersByUser($userId),

            'total_stock' => $this->repository
                ->totalStockByUser($userId),

            'low_stock_count' => $this->repository
                ->countLowStockProducts($userId),

            'inventory_value' => $this->repository
                ->inventoryValue($userId),

            'recent_movements' => $this->repository
                ->recentMovements($userId),
        ];
    }
}