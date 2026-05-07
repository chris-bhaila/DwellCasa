<?php

namespace App\Http\Controllers;

use App\Contracts\InventoryStockRepositoryInterface;
use App\Http\Requests\RestockSupplyRequest;
use App\Http\Requests\UseSupplyRequest;
use Illuminate\Http\JsonResponse;

class InventoryStockController extends Controller
{
    public function __construct(
        protected InventoryStockRepositoryInterface $repository
    ) {}

    public function restock(RestockSupplyRequest $request, int $itemId): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        try {
            $stock = $this->repository->restock(
                $itemId,
                $request->validated('quantity'),
                $request->validated('cost'),
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
            ->performedOn($stock)
            ->withProperties(['location_id' => $locationId])
            ->log("Restocked supply item — added {$request->quantity} units");

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data'    => $stock,
        ], 200);
    }

    public function logUsage(UseSupplyRequest $request, int $itemId): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        try {
            $stock = $this->repository->use(
                $itemId,
                $request->validated('quantity'),
                auth()->id(),
                $request->validated('room_id'),
                $request->validated('notes')
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => ['quantity' => [$e->getMessage()]],
            ], 422);
        }

        activity()
            ->causedBy($user)
            ->performedOn($stock)
            ->withProperties(['location_id' => $locationId])
            ->log("Logged supply usage — {$request->quantity} units used");

        return response()->json([
            'success' => true,
            'message' => 'Usage logged successfully',
            'data'    => $stock,
        ], 200);
    }

    public function logs(int $itemId): JsonResponse
    {
        $logs = $this->repository->logs($itemId);

        return response()->json([
            'success' => true,
            'message' => 'Stock logs fetched successfully',
            'data'    => $logs,
        ], 200);
    }

    public function adjust(\Illuminate\Http\Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'original_log_id' => 'required|integer|exists:inventory_logs,id',
            'adjustment'      => 'required|numeric|not_in:0',
            'reason'          => 'required|string|max:500',
        ]);

        try {
            $stock = $this->repository->adjust(
                $itemId,
                $request->integer('original_log_id'),
                (float) $request->input('adjustment'),
                auth()->id(),
                $request->input('reason')
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => ['adjustment' => [$e->getMessage()]],
            ], 422);
        }

        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        activity()
            ->causedBy($user)
            ->performedOn($stock)
            ->withProperties(['location_id' => $locationId])
            ->log("Adjusted supply stock for item ID {$itemId} by {$request->input('adjustment')} units");

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully',
            'data'    => $stock,
        ], 200);
    }
}
