<?php

namespace App\Repositories;

use App\Models\Location;
use App\Contracts\LocationRepositoryInterface;

class LocationRepository implements LocationRepositoryInterface
{
    public function all()
    {
        return Location::where('is_active', true)->orderBy('name')->get();
    }

    public function find($id)
    {
        return Location::findOrFail($id);
    }

    public function findBySlug(string $slug)
    {
        return Location::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function create(array $data)
    {
        return Location::create($data);
    }

    public function update($id, array $data)
    {
        $location = $this->find($id);
        $location->update($data);
        return $location;
    }

    public function delete($id)
    {
        $location = $this->find($id);
        $location->delete();
        return true;
    }
}