<?php

namespace App\Contracts;

use App\Models\InventoryCategory;
use Illuminate\Database\Eloquent\Collection;

interface InventoryCategoryRepositoryInterface
{
    /** Returns all categories for the active location, optionally filtered by type ('supply'|'equipment'). */
    public function all(?string $type = null): Collection;

    /** Find a single category by ID. */
    public function find(int $id): ?InventoryCategory;

    /** Create a new category. */
    public function create(array $data): InventoryCategory;

    /** Update an existing category. */
    public function update(int $id, array $data): InventoryCategory;

    /** Delete a category — throws if it has items attached. */
    public function delete(int $id): bool;
}
