<?php

namespace App\Contracts;

use App\Models\InventoryStock;
use Illuminate\Database\Eloquent\Collection;

interface InventoryStockRepositoryInterface
{
    /** Restock a supply item — adds quantity and cost, recalculates status, logs the action. */
    public function restock(int $itemId, float $quantity, float $cost, int $performedBy, ?string $notes = null): InventoryStock;

    /** Log usage of a supply item — subtracts quantity, recalculates status, logs the action. */
    public function use(int $itemId, float $quantity, int $performedBy, ?int $roomId = null, ?string $notes = null): InventoryStock;

    /** Get current stock for an item. */
    public function getStock(int $itemId): ?InventoryStock;

    /** Get log history for a supply item, ordered by created_at descending. */
    public function logs(int $itemId): Collection;

    /** Adjust stock against an original usage log entry within the correction window. */
    public function adjust(int $itemId, int $originalLogId, float $adjustment, int $performedBy, string $reason): InventoryStock;
}
