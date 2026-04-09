<?php

namespace App\Repositories;

use App\Models\Amenity;
use App\Contracts\AmenityRepositoryInterface;

class AmenityRepository implements AmenityRepositoryInterface
{
    public function all()
    {
        return Amenity::all();
    }

    public function find($id)
    {
        return Amenity::findOrFail($id);
    }

    public function create(array $data)
    {
        return Amenity::create($data);
    }

    public function update($id, array $data)
    {
        $amenity = $this->find($id);
        $amenity->update($data);
        return $amenity;
    }

    public function delete($id)
    {
        $amenity = $this->find($id);
        $amenity->delete();
        return true;
    }
}