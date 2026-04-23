<?php

namespace App\Http\Controllers;

use App\Contracts\InventoryRepositoryInterface;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    protected $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function index(Request $request)
    {
        $inventory = $this->inventoryRepository->all();
        return response()->json([
            'data'    => $inventory,
            'message' => 'Inventory fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $item = $this->inventoryRepository->find($id);
        return response()->json([
            'data'    => $item,
            'message' => 'Inventory item fetched successfully'
        ], 200);
    }

    public function store(StoreInventoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $item = $this->inventoryRepository->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Inventory item created successfully',
            'data'    => $item
        ], 201);
    }

    public function update(UpdateInventoryRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $existingItem = $this->inventoryRepository->find($id);
            if ($existingItem && $existingItem->image) {
                Storage::disk('public')->delete($existingItem->image);
            }
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $item = $this->inventoryRepository->update($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'Inventory item updated successfully',
            'data'    => $item
        ], 200);
    }

    public function destroy($id)
    {
        $item = $this->inventoryRepository->find($id);
        if ($item && $item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $this->inventoryRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Inventory item deleted successfully'
        ], 200);
    }
}