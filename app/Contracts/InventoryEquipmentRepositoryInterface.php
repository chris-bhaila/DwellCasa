<?php

namespace App\Contracts;

use App\Models\InventoryEquipment;
use Illuminate\Database\Eloquent\Collection;

interface InventoryEquipmentRepositoryInterface
{
    /** All equipment units for a given item. */
    public function allForItem(int $itemId): Collection;

    /** Find a single equipment unit by ID. */
    public function find(int $id): ?InventoryEquipment;

    /** Add a new physical equipment unit. */
    public function create(array $data): InventoryEquipment;

    /** Update an equipment unit (serial number, notes, purchase info). */
    public function update(int $id, array $data): InventoryEquipment;

    /** Assign a unit to a room. Logs the action. */
    public function assign(int $equipmentId, int $roomId, int $performedBy, ?string $notes = null): InventoryEquipment;

    /** Return a unit from its current room back to storage. Logs the action. */
    public function return(int $equipmentId, int $performedBy, ?string $notes = null): InventoryEquipment;

    /** Update the condition of a unit. Logs the condition change with previous and new condition. */
    public function updateCondition(int $equipmentId, string $condition, int $performedBy, ?string $notes = null): InventoryEquipment;

    /** Write off a unit — sets status to 'retired', logs it, soft deletes the record. */
    public function writeOff(int $equipmentId, int $performedBy, ?string $notes = null): bool;

    /** Get log history for a specific equipment unit, ordered by created_at descending. */
    public function logs(int $equipmentId): Collection;
}
