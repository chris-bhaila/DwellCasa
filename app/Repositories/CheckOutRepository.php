<?php

namespace App\Repositories;

use App\Models\CheckOut;
use App\Contracts\CheckOutRepositoryInterface;

class CheckOutRepository implements CheckOutRepositoryInterface
{
    public function all()
    {
        return CheckOut::with(['booking', 'room', 'checkedOutBy'])->get();
    }

    public function find($id)
    {
        return CheckOut::with(['booking', 'room', 'checkedOutBy'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return CheckOut::create($data);
    }

    public function update($id, array $data)
    {
        $checkOut = $this->find($id);
        $checkOut->update($data);
        return $checkOut;
    }

    public function delete($id)
    {
        $checkOut = $this->find($id);
        $checkOut->delete();
        return true;
    }
}