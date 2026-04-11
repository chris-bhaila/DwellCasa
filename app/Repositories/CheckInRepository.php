<?php

namespace App\Repositories;

use App\Models\CheckIn;
use App\Contracts\CheckInRepositoryInterface;

class CheckInRepository implements CheckInRepositoryInterface
{
    public function all()
    {
        return CheckIn::with(['booking', 'room', 'checkedInBy'])->get();
    }

    public function find($id)
    {
        return CheckIn::with(['booking', 'room', 'checkedInBy'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return CheckIn::create($data);
    }

    public function update($id, array $data)
    {
        $checkIn = $this->find($id);
        $checkIn->update($data);
        return $checkIn;
    }

    public function delete($id)
    {
        $checkIn = $this->find($id);
        $checkIn->delete();
        return true;
    }
}