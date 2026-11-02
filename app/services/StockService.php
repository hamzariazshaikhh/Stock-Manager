<?php

declare(strict_types=1);

final class StockService
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly ProductRepository $productRepository,
        private readonly StockMovementRepository $movementRepository
    ) {
    }

    public function recordMovement(
        int $userId,
        int $productId,
        string $movementType,
        mixed $quantity,
        mixed $note
    ): array {
        $movementType = trim($movementType);
        $note = $this->normalizeNote($note);

        $errors = [];

        if (
            !in_array(
                $movementType,
                ['in', 'out', 'adjustment'],
                true
            )
        ) {
            $errors[] = 'Invalid stock movement type.';
        }

        if (!isNonNegativeInteger($quantity)) {
            $errors[] = 'Quantity must be a non-negative integer.';
        }

        $quantity = (int) $quantity;

        if ($quantity === 0) {
            $errors[] = 'Quantity must be greater than zero.';
        }

        if ($errors !== []) {
            return $errors;
        }

        $quantityChange = match ($movementType) {
            'in' => $quantity,
            'out' => -$quantity,
            'adjustment' => $quantity,
        };

        $this->pdo->beginTransaction();

        try {
            $product = $this->productRepository->findByIdForUpdate(
                $productId,
                $userId
            );

            if ($product === null) {
                $this->pdo->rollBack();

                return ['Product not found.'];
            }

            $quantityBefore = (int) $product['quantity'];
            $quantityAfter = $quantityBefore + $quantityChange;

            if ($quantityAfter < 0) {
                $this->pdo->rollBack();

                return ['Insufficient stock.'];
            }

            $updated = $this->productRepository->updateQuantity(
                $productId,
                $userId,
                $quantityAfter
            );

            if (!$updated) {
                $this->pdo->rollBack();

                return ['Unable to update product quantity.'];
            }

            $this->movementRepository->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'movement_type' => $movementType,
                'quantity_change' => $quantityChange,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'note' => $note,
            ]);

            $this->pdo->commit();

            return [];
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log($exception->getMessage());

            return ['Unable to record stock movement.'];
        }
    }

    private function normalizeNote(mixed $value): ?string
    {
        $note = trim((string) ($value ?? ''));

        if ($note === '') {
            return null;
        }

        return mb_substr($note, 0, 255);
    }
}