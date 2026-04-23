<?php

namespace App\Contracts;

interface ReviewRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByToken(string $token);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}