<?php

namespace App\Repositories;

use App\Contracts\InventoryCategoryRepositoryInterface;
use App\Models\InventoryCategory;
use Illuminate\Database\Eloquent\Collection;

class InventoryCategoryRepository implements InventoryCategoryRepositoryInterface
{
    public function __construct(protected InventoryCategory $model) {}

    public function all(?string $type = null): Collection
    {
        return $this->model->newQuery()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->get();
    }

    public function find(int $id): ?InventoryCategory
    {
        return $this->model->newQuery()->find($id);
    }

    public function create(array $data): InventoryCategory
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int $id, array $data): InventoryCategory
    {
        $category = $this->model->newQuery()->findOrFail($id);
        $category->update($data);

        return $category;
    }

    public function delete(int $id): bool
    {
        $category = $this->model->newQuery()->findOrFail($id);

        if ($category->items()->exists()) {
            throw new \Exception('Cannot delete a category that has items attached.');
        }

        return $category->delete();
    }
}
