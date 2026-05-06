<?php

namespace App\Contracts;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Collection;

interface InventoryItemRepositoryInterface
{
    /** All items for the active location, optionally filtered by type ('supply'|'equipment'). */
    public function all(?string $type = null): Collection;

    /** Find a single item by ID, with its stock loaded (for supply items). */
    public function find(int $id): ?InventoryItem;

    /** Create a new item in the catalog. For supply items, also creates an InventoryStock record with quantity 0. */
    public function create(array $data): InventoryItem;

    /** Update an existing item. */
    public function update(int $id, array $data): InventoryItem;

    /** Soft delete an item. */
    public function delete(int $id): bool;

    /** Returns all supply items with their stock loaded, optionally filtered by status ('available'|'low_stock'|'out_of_stock'). */
    public function suppliesWithStock(?string $status = null): Collection;

    /** Returns all equipment items with their units loaded. */
    public function equipmentWithUnitCounts(): Collection;
}
