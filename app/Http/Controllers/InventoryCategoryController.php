<?php

namespace App\Http\Controllers;

use App\Contracts\InventoryCategoryRepositoryInterface;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryCategoryController extends Controller
{
    public function __construct(
        protected InventoryCategoryRepositoryInterface $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');

        $categories = $this->repository->all($type);

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully',
            'data'    => $categories,
        ], 200);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $data = array_merge($request->validated(), ['location_id' => $locationId]);

        $category = $this->repository->create($data);

        activity()
            ->causedBy($user)
            ->performedOn($category)
            ->withProperties(['location_id' => $locationId])
            ->log("Created inventory category {$category->name}");

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data'    => $category,
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        $category = $this->repository->update($id, $request->validated());

        activity()
            ->causedBy($user)
            ->performedOn($category)
            ->withProperties(['location_id' => $locationId])
            ->log("Updated inventory category {$category->name}");

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data'    => $category,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
        abort_if(!$locationId, 422, 'No location selected.');

        try {
            $category = $this->repository->find($id);
            $this->repository->delete($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => [],
            ], 422);
        }

        activity()
            ->causedBy($user)
            ->performedOn($category)
            ->withProperties(['location_id' => $locationId])
            ->log("Deleted inventory category {$category->name}");

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
            'data'    => null,
        ], 200);
    }
}
