<?php

namespace App\Repositories;

use App\Models\WebsiteInfo;
use App\Contracts\WebsiteInfoRepositoryInterface;

class WebsiteInfoRepository implements WebsiteInfoRepositoryInterface
{
    public function getForLocation(int $locationId): WebsiteInfo
    {
        return WebsiteInfo::withoutGlobalScopes()
            ->where('location_id', $locationId)
            ->firstOrNew(['location_id' => $locationId]);
    }

    public function getGlobal(): ?WebsiteInfo
    {
        // Falls back to the first active row — for the brand homepage
        // where no location context exists. Replace with a dedicated
        // global row (location_id = null) once your schema supports it.
        return WebsiteInfo::withoutGlobalScopes()
            ->whereNull('location_id')
            ->first()
            ?? WebsiteInfo::withoutGlobalScopes()->first();
    }

    public function update(array $data): WebsiteInfo
    {
        $locationId = $data['location_id'] ?? null;

        $info = WebsiteInfo::withoutGlobalScopes()
            ->where('location_id', $locationId)
            ->first();

        if (!$info) {
            $info = new WebsiteInfo();
        }

        $info->fill($data)->save();
        return $info;
    }

    public function updateOrCreateForLocation(int $locationId, array $data): WebsiteInfo
    {
        return WebsiteInfo::withoutGlobalScopes()->updateOrCreate(
            ['location_id' => $locationId],
            $data
        );
    }
}
