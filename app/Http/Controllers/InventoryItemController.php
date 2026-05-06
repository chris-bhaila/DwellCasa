<?php

namespace App\Http\Controllers;

use App\Contracts\InventoryItemRepositoryInterface;
use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use App\Models\InventoryEquipment;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\InventoryStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function __construct(
        protected InventoryItemRepositoryInterface $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');

        $items = match ($type) {
            'supply'    => $this->repository->suppliesWithStock(),
            'equipment' => $this->repository->equipmentWithUnitCounts(),
            default     => $this->repository->all(),
        };

        return response()->json([
            'success' => true,
            'message' => 'Items fetched successfully',
            'data'    => $items,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->repository->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item fetched successfully',
            'data'    => $item,
        ], 200);
    }

    public function store(StoreInventoryItemRequest $request): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $data = array_merge($request->validated(), ['location_id' => $locationId]);

        $item = $this->repository->create($data);

        activity()
            ->causedBy($user)
            ->performedOn($item)
            ->withProperties(['location_id' => $locationId])
            ->log("Created inventory item {$item->name} ({$item->type})");

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data'    => $item,
        ], 201);
    }

    public function update(UpdateInventoryItemRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $item = $this->repository->update($id, $request->validated());

        activity()
            ->causedBy($user)
            ->performedOn($item)
            ->withProperties(['location_id' => $locationId])
            ->log("Updated inventory item {$item->name}");

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data'    => $item,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $item = $this->repository->find($id);

        $this->repository->delete($id);

        activity()
            ->causedBy($user)
            ->performedOn($item)
            ->withProperties(['location_id' => $locationId])
            ->log("Deleted inventory item {$item->name}");

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully',
            'data'    => null,
        ], 200);
    }

    public function inventoryDashboard()
    {
        // Supply stats
        $totalSupplies    = InventoryItem::where('type', 'supply')->count();
        $lowStockCount    = InventoryStock::where('status', 'low_stock')->count();
        $outOfStockCount  = InventoryStock::where('status', 'out_of_stock')->count();

        // Equipment stats
        $totalEquipment   = InventoryEquipment::count();
        $assignedCount    = InventoryEquipment::where('status', 'assigned')->count();
        $damagedCount     = InventoryEquipment::whereIn('condition', ['damaged', 'under_repair'])->count();

        // Recent activity — last 5 log entries with item name
        $recentLogs = InventoryLog::with(['item', 'performedBy'])
            ->latest()
            ->take(5)
            ->get();

        // Recent supplies — last 5 supply items by updated_at
        $recentSupplies = InventoryItem::where('type', 'supply')
            ->with('stock')
            ->latest()
            ->take(5)
            ->get();

        // Recent equipment — last 5 equipment units by updated_at
        $recentEquipment = InventoryEquipment::with(['item', 'currentRoom'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.inventory.index', compact(
            'totalSupplies', 'lowStockCount', 'outOfStockCount',
            'totalEquipment', 'assignedCount', 'damagedCount',
            'recentLogs', 'recentSupplies', 'recentEquipment'
        ));
    }

    public function suppliesPage()
    {
        return view('admin.inventory.supplies');
    }

    public function equipmentPage()
    {
        return view('admin.inventory.equipment');
    }
}
