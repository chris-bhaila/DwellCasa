<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Contracts\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function all()
    {
        return Inventory::latest()->get();
    }

    public function find($id)
    {
        return Inventory::findOrFail($id);
    }

    public function create(array $data)
    {
        return Inventory::create($data);
    }

    public function update($id, array $data)
    {
        $inventory = $this->find($id);
        $inventory->update($data);
        return $inventory;
    }

    public function delete($id)
    {
        $inventory = $this->find($id);
        $inventory->delete();
        return true;
    }
}