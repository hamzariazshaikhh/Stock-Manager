<?php

declare(strict_types=1);

final class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SupplierRepository $supplierRepository
    ) {
    }

    public function create(array $input, int $userId): array
    {
        $data = $this->normalize($input);
        $errors = $this->validate($data, $userId);

        if ($errors !== []) {
            return $errors;
        }

        if (
            $this->productRepository->skuExistsForUser(
                $data['sku'],
                $userId
            )
        ) {
            return ['A product with this SKU already exists.'];
        }

        $this->productRepository->create($userId, $data);

        return [];
    }

    public function update(
        int $productId,
        array $input,
        int $userId
    ): array {
        $product = $this->productRepository->findByIdForUser(
            $productId,
            $userId
        );

        if ($product === null) {
            return ['Product not found.'];
        }

        $data = $this->normalize($input);

        /*
         * Quantity is intentionally taken from the existing product.
         * Product editing must not silently change inventory.
         */
        $data['quantity'] = (int) $product['quantity'];

        $errors = $this->validate($data, $userId);

        if ($errors !== []) {
            return $errors;
        }

        if (
            $this->productRepository->skuExistsForUser(
                $data['sku'],
                $userId,
                $productId
            )
        ) {
            return ['A product with this SKU already exists.'];
        }

        $this->productRepository->update(
            $productId,
            $userId,
            $data
        );

        return [];
    }

    public function delete(int $productId, int $userId): bool
    {
        return $this->productRepository->delete(
            $productId,
            $userId
        );
    }

    private function normalize(array $input): array
    {
        return [
            'category_id' => $this->normalizeForeignKey(
                $input['category_id'] ?? null
            ),
            'supplier_id' => $this->normalizeForeignKey(
                $input['supplier_id'] ?? null
            ),
            'name' => trim((string) ($input['name'] ?? '')),
            'sku' => strtoupper(
                trim((string) ($input['sku'] ?? ''))
            ),
            'description' => $this->nullableString(
                $input['description'] ?? null
            ),
            'quantity' => $this->normalizeInteger(
                $input['quantity'] ?? null
            ),
            'reorder_level' => $this->normalizeInteger(
                $input['reorder_level'] ?? null
            ),
            'unit_price' => trim(
                (string) ($input['unit_price'] ?? '')
            ),
        ];
    }

    private function validate(
        array $data,
        int $userId
    ): array {
        $errors = [];

        $nameError = validateRequiredString(
            $data['name'],
            'Product name',
            150
        );

        if ($nameError !== null) {
            $errors[] = $nameError;
        }

        $skuError = validateRequiredString(
            $data['sku'],
            'SKU',
            100
        );

        if ($skuError !== null) {
            $errors[] = $skuError;
        }

        if (!isNonNegativeInteger($data['quantity'])) {
            $errors[] = 'Quantity must be a non-negative integer.';
        }

        if (!isNonNegativeInteger($data['reorder_level'])) {
            $errors[] = 'Reorder level must be a non-negative integer.';
        }

        $priceError = validateMoney(
            $data['unit_price'],
            'Unit price'
        );

        if ($priceError !== null) {
            $errors[] = $priceError;
        }

        if (
            $data['category_id'] !== null
            && $this->categoryRepository->findByIdForUser(
                $data['category_id'],
                $userId
            ) === null
        ) {
            $errors[] = 'Selected category is invalid.';
        }

        if (
            $data['supplier_id'] !== null
            && $this->supplierRepository->findByIdForUser(
                $data['supplier_id'],
                $userId
            ) === null
        ) {
            $errors[] = 'Selected supplier is invalid.';
        }

        return $errors;
    }

    private function normalizeForeignKey(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (!isPositiveInteger($value)) {
            return -1;
        }

        return (int) $value;
    }

    private function normalizeInteger(mixed $value): mixed
    {
        if (!isNonNegativeInteger($value)) {
            return $value;
        }

        return (int) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}