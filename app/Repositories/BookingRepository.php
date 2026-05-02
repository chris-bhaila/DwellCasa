<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Contracts\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    public function all()
    {
        return Booking::with(['guest', 'roomType', 'room', 'payments'])->latest()->get();
    }

    public function find($id)
    {
        return Booking::with(['guest', 'roomType', 'room', 'payments'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Booking::create($data);
    }

    public function update($id, array $data)
    {
        $booking = $this->find($id);
        $booking->update($data);
        return $booking;
    }

    public function delete($id)
    {
        $booking = $this->find($id);
        $booking->delete();
        return true;
    }

    public function trashed()
    {
        return Booking::onlyTrashed()->with(['guest', 'roomType', 'room'])->latest('deleted_at')->get();
    }

    public function restore($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->restore();
        return $booking;
    }

    public function forceDelete($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->forceDelete();
        return true;
    }
}