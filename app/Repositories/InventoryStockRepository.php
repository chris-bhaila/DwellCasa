<?php

namespace App\Repositories;

use App\Contracts\InventoryStockRepositoryInterface;
use App\Models\InventoryLog;
use App\Models\InventoryStock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryStockRepository implements InventoryStockRepositoryInterface
{
    public function __construct(protected InventoryStock $model) {}

    public function restock(int $itemId, float $quantity, float $cost, int $performedBy, ?string $notes = null): InventoryStock
    {
        return DB::transaction(function () use ($itemId, $quantity, $cost, $performedBy, $notes) {
            $stock = $this->model->newQuery()
                ->with('item')
                ->where('inventory_item_id', $itemId)
                ->firstOrFail();

            $stock->quantity_on_hand += $quantity;
            $stock->total_cost += $cost;
            $stock->status = $this->resolveStatus($stock->quantity_on_hand, $stock->item->minimum_stock);
            $stock->save();

            InventoryLog::create([
                'location_id'       => $stock->location_id,
                'inventory_item_id' => $itemId,
                'action'            => 'restocked',
                'quantity'          => $quantity,
                'cost'              => $cost,
                'performed_by'      => $performedBy,
                'notes'             => $notes,
            ]);

            return $stock;
        });
    }

    public function use(int $itemId, float $quantity, int $performedBy, ?int $roomId = null, ?string $notes = null): InventoryStock
    {
        return DB::transaction(function () use ($itemId, $quantity, $performedBy, $roomId, $notes) {
            $stock = $this->model->newQuery()
                ->with('item')
                ->where('inventory_item_id', $itemId)
                ->firstOrFail();

            if ($stock->quantity_on_hand < $quantity) {
                throw new \Exception('Insufficient stock.');
            }

            $stock->quantity_on_hand -= $quantity;
            $stock->status = $this->resolveStatus($stock->quantity_on_hand, $stock->item->minimum_stock);
            $stock->save();

            InventoryLog::create([
                'location_id'       => $stock->location_id,
                'inventory_item_id' => $itemId,
                'action'            => 'used',
                'quantity'          => $quantity,
                'room_id'           => $roomId,
                'performed_by'      => $performedBy,
                'notes'             => $notes,
            ]);

            return $stock;
        });
    }

    public function getStock(int $itemId): ?InventoryStock
    {
        return $this->model->newQuery()
            ->where('inventory_item_id', $itemId)
            ->first();
    }

    public function logs(int $itemId): Collection
    {
        return InventoryLog::where('inventory_item_id', $itemId)
            ->whereIn('action', ['restocked', 'used'])
            ->orderByDesc('created_at')
            ->get();
    }

    private function resolveStatus(float $quantity, float $minimumStock): string
    {
        if ($quantity <= 0) {
            return 'out_of_stock';
        }

        if ($quantity <= $minimumStock) {
            return 'low_stock';
        }

        return 'available';
    }
}
