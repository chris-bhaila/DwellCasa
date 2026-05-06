<?php

namespace App\Http\Controllers;

use App\Contracts\InventoryEquipmentRepositoryInterface;
use App\Http\Requests\AssignEquipmentRequest;
use App\Http\Requests\StoreEquipmentUnitRequest;
use App\Http\Requests\UpdateConditionRequest;
use App\Http\Requests\UpdateEquipmentUnitRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryEquipmentController extends Controller
{
    public function __construct(
        protected InventoryEquipmentRepositoryInterface $repository
    ) {}

    public function index(int $itemId): JsonResponse
    {
        $units = $this->repository->allForItem($itemId);

        return response()->json([
            'success' => true,
            'message' => 'Equipment units fetched successfully',
            'data'    => $units,
        ], 200);
    }

    public function store(StoreEquipmentUnitRequest $request): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $data = array_merge($request->validated(), ['location_id' => $locationId]);

        $equipment = $this->repository->create($data);

        activity()
            ->causedBy($user)
            ->performedOn($equipment)
            ->withProperties(['location_id' => $locationId])
            ->log("Added equipment unit for item ID {$equipment->inventory_item_id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipment unit added successfully',
            'data'    => $equipment,
        ], 201);
    }

    public function update(UpdateEquipmentUnitRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $equipment = $this->repository->update($id, $request->validated());

        activity()
            ->causedBy($user)
            ->performedOn($equipment)
            ->withProperties(['location_id' => $locationId])
            ->log("Updated equipment unit ID {$id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipment unit updated successfully',
            'data'    => $equipment,
        ], 200);
    }

    public function assign(AssignEquipmentRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        try {
            $equipment = $this->repository->assign(
                $id,
                $request->validated('room_id'),
                auth()->id(),
                $request->validated('notes')
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => [],
            ], 422);
        }

        activity()
            ->causedBy($user)
            ->performedOn($equipment)
            ->withProperties(['location_id' => $locationId])
            ->log("Assigned equipment unit ID {$id} to room ID {$request->room_id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipment assigned successfully',
            'data'    => $equipment,
        ], 200);
    }

    public function return(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        try {
            $equipment = $this->repository->return($id, auth()->id(), $request->input('notes'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => [],
            ], 422);
        }

        activity()
            ->causedBy($user)
            ->performedOn($equipment)
            ->withProperties(['location_id' => $locationId])
            ->log("Returned equipment unit ID {$id} from room");

        return response()->json([
            'success' => true,
            'message' => 'Equipment returned successfully',
            'data'    => $equipment,
        ], 200);
    }

    public function updateCondition(UpdateConditionRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $equipment = $this->repository->updateCondition(
            $id,
            $request->validated('condition'),
            auth()->id(),
            $request->validated('notes')
        );

        activity()
            ->causedBy($user)
            ->performedOn($equipment)
            ->withProperties(['location_id' => $locationId])
            ->log("Updated condition of equipment unit ID {$id} to {$request->condition}");

        return response()->json([
            'success' => true,
            'message' => 'Condition updated successfully',
            'data'    => $equipment,
        ], 200);
    }

    public function writeOff(int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $equipment = $this->repository->find($id);

        try {
            $this->repository->writeOff($id, auth()->id());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => [],
            ], 422);
        }

        activity()
            ->causedBy($user)
            ->performedOn($equipment)
            ->withProperties(['location_id' => $locationId])
            ->log("Wrote off equipment unit ID {$id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipment written off successfully',
            'data'    => null,
        ], 200);
    }

    public function logs(int $id): JsonResponse
    {
        $logs = $this->repository->logs($id);

        return response()->json([
            'success' => true,
            'message' => 'Equipment logs fetched successfully',
            'data'    => $logs,
        ], 200);
    }
}
