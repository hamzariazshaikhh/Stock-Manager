<?php

declare(strict_types=1);

final class SupplierService
{
    public function __construct(
        private readonly SupplierRepository $repository
    ) {
    }

    public function validate(array $input): array
    {
        $errors = [];

        $nameError = validateRequiredString(
            $input['name'] ?? null,
            'Supplier name',
            150
        );

        if ($nameError !== null) {
            $errors[] = $nameError;
        }

        if (($input['email'] ?? '') !== '') {
            $emailError = validateEmail(
                $input['email'],
                'Supplier email'
            );

            if ($emailError !== null) {
                $errors[] = $emailError;
            }
        }

        return $errors;
    }

    public function create(array $input, int $userId): array
    {
        $input = $this->normalize($input);
        $errors = $this->validate($input);

        if ($errors !== []) {
            return $errors;
        }

        $this->repository->create(
            $userId,
            $input['name'],
            $input['email'],
            $input['phone'],
            $input['address']
        );

        return [];
    }

    public function update(
        int $supplierId,
        array $input,
        int $userId
    ): array {
        $input = $this->normalize($input);
        $errors = $this->validate($input);

        if ($errors !== []) {
            return $errors;
        }

        if (
            $this->repository->findByIdForUser(
                $supplierId,
                $userId
            ) === null
        ) {
            return ['Supplier not found.'];
        }

        $this->repository->update(
            $supplierId,
            $userId,
            $input['name'],
            $input['email'],
            $input['phone'],
            $input['address']
        );

        return [];
    }

    public function delete(int $supplierId, int $userId): bool
    {
        return $this->repository->delete($supplierId, $userId);
    }

    private function normalize(array $input): array
    {
        return [
            'name' => trim((string) ($input['name'] ?? '')),
            'email' => $this->nullableString($input['email'] ?? null),
            'phone' => $this->nullableString($input['phone'] ?? null),
            'address' => $this->nullableString($input['address'] ?? null),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}