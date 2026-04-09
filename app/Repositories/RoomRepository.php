<?php

namespace App\Repositories;

use App\Models\Room;
use App\Contracts\RoomRepositoryInterface;

class RoomRepository implements RoomRepositoryInterface
{
    public function all()
    {
        return Room::all();
    }

    public function find($id)
    {
        return Room::findOrFail($id);
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
}