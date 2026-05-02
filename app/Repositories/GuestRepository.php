<?php

namespace App\Repositories;

use App\Models\Guest;
use App\Contracts\GuestRepositoryInterface;

class GuestRepository implements GuestRepositoryInterface
{
    public function all()
    {
        return Guest::with(['bookings', 'payments'])->get();
    }

    public function find($id)
    {
        return Guest::with(['bookings', 'payments'])->findOrFail($id);
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

    public function trashed()
    {
        return Guest::onlyTrashed()->with(['bookings', 'payments'])->latest('deleted_at')->get();
    }

    public function restore($id)
    {
        $guest = Guest::onlyTrashed()->findOrFail($id);
        $guest->restore();
        return $guest;
    }

    public function forceDelete($id)
    {
        $guest = Guest::onlyTrashed()->findOrFail($id);
        $guest->forceDelete();
        return true;
    }
}