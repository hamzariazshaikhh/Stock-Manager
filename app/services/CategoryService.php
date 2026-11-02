<?php

declare(strict_types=1);

final class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $repository
    ) {
    }

    public function validateName(string $name): array
    {
        $errors = [];

        $requiredError = validateRequiredString(
            $name,
            'Category name',
            120
        );

        if ($requiredError !== null) {
            $errors[] = $requiredError;
        }

        return $errors;
    }

    public function create(
        string $name,
        int $userId
    ): array {
        $name = trim($name);
        $errors = $this->validateName($name);

        if ($errors !== []) {
            return $errors;
        }

        if ($this->repository->existsByNameForUser($name, $userId)) {
            return ['A category with this name already exists.'];
        }

        $this->repository->create($name, $userId);

        return [];
    }

    public function update(
        int $categoryId,
        string $name,
        int $userId
    ): array {
        $name = trim($name);
        $errors = $this->validateName($name);

        if ($errors !== []) {
            return $errors;
        }

        $category = $this->repository->findByIdForUser(
            $categoryId,
            $userId
        );

        if ($category === null) {
            return ['Category not found.'];
        }

        if (
            $this->repository->existsByNameForUser(
                $name,
                $userId,
                $categoryId
            )
        ) {
            return ['A category with this name already exists.'];
        }

        $this->repository->update(
            $categoryId,
            $userId,
            $name
        );

        return [];
    }

    public function delete(
        int $categoryId,
        int $userId
    ): bool {
        return $this->repository->delete(
            $categoryId,
            $userId
        );
    }
}