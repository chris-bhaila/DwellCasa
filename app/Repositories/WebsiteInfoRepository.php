<?php

namespace App\Repositories;

use App\Models\WebsiteInfo;
use App\Contracts\WebsiteInfoRepositoryInterface;

class WebsiteInfoRepository implements WebsiteInfoRepositoryInterface
{
    public function get()
    {
        return WebsiteInfo::firstOrCreate([]);
    }

    public function update(array $data)
    {
        $info = WebsiteInfo::firstOrCreate([]);
        $info->update($data);
        return $info;
    }
}