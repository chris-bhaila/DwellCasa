<?php

namespace App\Repositories;

use App\Models\RoomType;
use App\Contracts\RoomTypeRepositoryInterface;

class RoomTypeRepository implements RoomTypeRepositoryInterface
{
    public function all()
    {
        return RoomType::with(['rooms', 'amenities'])->get();
    }

    public function find($id)
    {
        return RoomType::with(['rooms', 'amenities'])->findOrFail($id);
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

    public function trashed()
    {
        return RoomType::onlyTrashed()->with(['rooms', 'amenities'])->latest('deleted_at')->get();
    }

    public function restore($id)
    {
        $roomType = RoomType::onlyTrashed()->findOrFail($id);
        $roomType->restore();
        return $roomType;
    }

    public function forceDelete($id)
    {
        $roomType = RoomType::onlyTrashed()->findOrFail($id);
        $roomType->forceDelete();
        return true;
    }
}