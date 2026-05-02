<?php

namespace App\Contracts;

interface BookingRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function trashed();
    public function restore($id);
    public function forceDelete($id);
}