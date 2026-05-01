<?php

namespace App\Contracts;

interface WebsiteInfoRepositoryInterface
{
    public function getForLocation(int $locationId): ?\App\Models\WebsiteInfo;
    public function getGlobal(): ?\App\Models\WebsiteInfo;
    public function update(array $data): \App\Models\WebsiteInfo;
    public function updateOrCreateForLocation(int $locationId, array $data): \App\Models\WebsiteInfo;
}