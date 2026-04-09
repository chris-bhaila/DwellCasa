<?php

namespace App\Repositories;

use App\Models\Guest;
use App\Contracts\GuestRepositoryInterface;

class GuestRepository implements GuestRepositoryInterface
{
    public function all()
    {
        return Guest::all();
    }

    public function find($id)
    {
        return Guest::findOrFail($id);
    }

    public function create(array $data)
    {
        return Guest::create($data);
    }

    public function update($id, array $data)
    {
        $guest = $this->find($id);
        $guest->update($data);
        return $guest;
    }

    public function delete($id)
    {
        $guest = $this->find($id);
        $guest->delete();
        return true;
    }
}