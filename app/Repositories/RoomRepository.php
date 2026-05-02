<?php

namespace App\Repositories;

use App\Models\Room;
use App\Contracts\RoomRepositoryInterface;

class RoomRepository implements RoomRepositoryInterface
{
    public function all()
    {
        return Room::with(['roomType', 'amenities'])->get();
    }

    public function find($id)
    {
        return Room::with(['roomType', 'amenities'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Room::create($data);
    }

    public function update($id, array $data)
    {
        $room = $this->find($id);
        $room->update($data);
        return $room;
    }

    public function delete($id)
    {
        $room = $this->find($id);
        $room->delete();
        return true;
    }

    public function trashed()
    {
        return Room::onlyTrashed()->with(['roomType', 'amenities'])->latest('deleted_at')->get();
    }

    public function restore($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);
        $room->restore();
        return $room;
    }

    public function forceDelete($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);
        $room->forceDelete();
        return true;
    }
}