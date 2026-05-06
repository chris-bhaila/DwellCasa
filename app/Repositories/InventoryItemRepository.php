<?php

namespace App\Repositories;

use App\Contracts\InventoryItemRepositoryInterface;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryItemRepository implements InventoryItemRepositoryInterface
{
    public function __construct(protected InventoryItem $model) {}

    public function all(?string $type = null): Collection
    {
        return $this->model->newQuery()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->get();
    }

    public function find(int $id): ?InventoryItem
    {
        return $this->model->newQuery()->with('stock')->find($id);
    }

    public function create(array $data): InventoryItem
    {
        return DB::transaction(function () use ($data) {
            $item = $this->model->newQuery()->create($data);

            if ($data['type'] === 'supply') {
                InventoryStock::create([
                    'inventory_item_id' => $item->id,
                    'location_id'       => $data['location_id'],
                    'quantity_on_hand'  => 0,
                    'status'            => 'out_of_stock',
                    'total_cost'        => 0,
                ]);
            }

            return $item;
        });
    }

    public function update(int $id, array $data): InventoryItem
    {
        $item = $this->model->newQuery()->findOrFail($id);
        $item->update($data);

        return $item;
    }

    public function delete(int $id): bool
    {
        $item = $this->model->newQuery()->findOrFail($id);

        return $item->delete();
    }

    public function suppliesWithStock(?string $status = null): Collection
    {
        return $this->model->newQuery()
            ->where('type', 'supply')
            ->with('stock')
            ->when($status, fn ($q) => $q->whereHas('stock', fn ($s) => $s->where('status', $status)))
            ->get();
    }

    public function equipmentWithUnitCounts(): Collection
    {
        return $this->model->newQuery()
            ->where('type', 'equipment')
            ->with('equipment')
            ->get();
    }
}
