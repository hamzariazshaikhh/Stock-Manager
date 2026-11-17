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


if (
    $input['phone'] !== null
    && mb_strlen($input['phone']) > 50
) {
    $errors[] = 'Supplier phone must not exceed 50 characters.';
}

if (
    $input['address'] !== null
    && mb_strlen($input['address']) > 255
) {
    $errors[] = 'Supplier address must not exceed 255 characters.';
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

           // Log action
        logUserAction(
            $this->repository->getPdo(),
            $userId,
            'SUPPLIER_CREATE',
            "Created supplier: {$input['name']}"
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

          // Log action
        logUserAction(
            $this->repository->getPdo(),
            $userId,
            'SUPPLIER_UPDATE',
            "Updated supplier ID {$supplierId}: {$input['name']}"
        );


        return [];
    }

    public function delete(int $supplierId, int $userId): bool
    {
        $deleted = $this->repository->delete($supplierId, $userId);

        if ($deleted) {
            // Log action
            logUserAction(
                $this->repository->getPdo(),
                $userId,
                'SUPPLIER_DELETE',
                "Deleted supplier ID {$supplierId}"
            );
        }

        return $deleted;
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