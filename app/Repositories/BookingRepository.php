<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Contracts\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    public function all()
    {
        return Booking::all();
    }

    public function find($id)
    {
        return Booking::findOrFail($id);
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
}