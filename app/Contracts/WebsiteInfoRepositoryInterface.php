<?php

namespace App\Contracts;

interface WebsiteInfoRepositoryInterface
{
    public function get();
    public function update(array $data);
}