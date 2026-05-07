<?php

namespace App\Repositories;

use App\Contracts\InventoryEquipmentRepositoryInterface;
use App\Models\InventoryEquipment;
use App\Models\InventoryLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryEquipmentRepository implements InventoryEquipmentRepositoryInterface
{
    public function __construct(protected InventoryEquipment $model) {}

    public function allForItem(int $itemId): Collection
    {
        return $this->model->newQuery()
            ->where('inventory_item_id', $itemId)
            ->with('currentRoom')
            ->get();
    }

    public function find(int $id): ?InventoryEquipment
    {
        return $this->model->newQuery()->find($id);
    }

    public function create(array $data): InventoryEquipment
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int $id, array $data): InventoryEquipment
    {
        $equipment = $this->model->newQuery()->findOrFail($id);
        $equipment->update($data);

        return $equipment;
    }

    public function assign(int $equipmentId, int $roomId, int $performedBy, ?string $notes = null): InventoryEquipment
    {
        return DB::transaction(function () use ($equipmentId, $roomId, $performedBy, $notes) {
            $equipment = $this->model->newQuery()->findOrFail($equipmentId);

            if ($equipment->status === 'assigned') {
                throw new \Exception('Equipment is already assigned to a room. Return it first.');
            }

            $equipment->current_room_id = $roomId;
            $equipment->status = 'assigned';
            $equipment->save();

            InventoryLog::create([
                'location_id'              => $equipment->location_id,
                'inventory_item_id'        => $equipment->inventory_item_id,
                'inventory_equipment_id'   => $equipmentId,
                'action'                   => 'assigned',
                'room_id'                  => $roomId,
                'performed_by'             => $performedBy,
                'notes'                    => $notes,
            ]);

            return $equipment;
        });
    }

    public function return(int $equipmentId, int $performedBy, ?string $notes = null): InventoryEquipment
    {
        return DB::transaction(function () use ($equipmentId, $performedBy, $notes) {
            $equipment = InventoryEquipment::findOrFail($equipmentId);

            if ($equipment->status !== 'assigned') {
                throw new \Exception('Equipment is not currently assigned.');
            }

            $previousRoomId = $equipment->current_room_id;

            $equipment->current_room_id = null;
            $equipment->status = 'available';
            $equipment->save();

            InventoryLog::create([
                'location_id'              => $equipment->location_id,
                'inventory_item_id'        => $equipment->inventory_item_id,
                'inventory_equipment_id'   => $equipmentId,
                'action'                   => 'returned',
                'room_id'                  => $previousRoomId,
                'performed_by'             => $performedBy,
                'notes'                    => $notes,
            ]);

            return $equipment;
        });
    }

    public function updateCondition(int $equipmentId, string $condition, int $performedBy, ?string $notes = null): InventoryEquipment
    {
        return DB::transaction(function () use ($equipmentId, $condition, $performedBy, $notes) {
            $equipment = $this->model->newQuery()->findOrFail($equipmentId);

            $previous = $equipment->condition;

            $equipment->condition = $condition;
            $equipment->save();

            InventoryLog::create([
                'location_id'              => $equipment->location_id,
                'inventory_item_id'        => $equipment->inventory_item_id,
                'inventory_equipment_id'   => $equipmentId,
                'action'                   => 'condition_changed',
                'previous_condition'       => $previous,
                'new_condition'            => $condition,
                'performed_by'             => $performedBy,
                'notes'                    => $notes,
            ]);

            return $equipment;
        });
    }

    public function writeOff(int $equipmentId, int $performedBy, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($equipmentId, $performedBy, $notes) {
            $equipment = $this->model->newQuery()->findOrFail($equipmentId);

            $equipment->status = 'retired';
            $equipment->save();

            InventoryLog::create([
                'location_id'              => $equipment->location_id,
                'inventory_item_id'        => $equipment->inventory_item_id,
                'inventory_equipment_id'   => $equipmentId,
                'action'                   => 'written_off',
                'performed_by'             => $performedBy,
                'notes'                    => $notes,
            ]);

            return $equipment->delete();
        });
    }

    public function logs(int $equipmentId): Collection
    {
        return InventoryLog::where('inventory_equipment_id', $equipmentId)
            ->with('performedBy', 'room')
            ->orderByDesc('created_at')
            ->get();
    }

    public function correct(int $equipmentId, int $originalLogId, int $performedBy, ?string $reason = null): InventoryEquipment
    {
        return DB::transaction(function () use ($equipmentId, $originalLogId, $performedBy, $reason) {
            $equipment   = InventoryEquipment::findOrFail($equipmentId);
            $originalLog = InventoryLog::findOrFail($originalLogId);

            if ($originalLog->inventory_equipment_id !== $equipmentId) {
                throw new \Exception('Log entry does not belong to this equipment unit.');
            }

            if ($originalLog->action !== 'assigned') {
                throw new \Exception('Only assignment logs can be corrected.');
            }

            if (!$originalLog->isWithinCorrectionWindow()) {
                throw new \Exception('Correction window has expired for this log entry.');
            }

            $previousRoomId = $equipment->current_room_id;
            $equipment->current_room_id = null;
            $equipment->status = 'available';
            $equipment->save();

            InventoryLog::create([
                'location_id'            => $equipment->location_id,
                'inventory_item_id'      => $equipment->inventory_item_id,
                'inventory_equipment_id' => $equipmentId,
                'action'                 => 'corrected',
                'room_id'                => $previousRoomId,
                'performed_by'           => $performedBy,
                'notes'                  => $reason,
                'corrected_log_id'       => $originalLogId,
            ]);

            return $equipment;
        });
    }
}
