<?php

namespace App\Contracts;

interface LocationRepositoryInterface
{
    public function all();
    public function find($id);
    public function findBySlug(string $slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}