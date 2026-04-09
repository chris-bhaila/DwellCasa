<?php

namespace App\Repositories;

use App\Models\RoomType;
use App\Contracts\RoomTypeRepositoryInterface;

class RoomTypeRepository implements RoomTypeRepositoryInterface
{
    public function all()
    {
        return RoomType::all();
    }

    public function find($id)
    {
        return RoomType::findOrFail($id);
    }

    public function create(array $data)
    {
        return RoomType::create($data);
    }

    public function update($id, array $data)
    {
        $roomType = $this->find($id);
        $roomType->update($data);
        return $roomType;
    }

    public function delete($id)
    {
        $roomType = $this->find($id);
        $roomType->delete();
        return true;
    }
}