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

    public function use(UseSupplyRequest $request, int $itemId): JsonResponse
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
}
