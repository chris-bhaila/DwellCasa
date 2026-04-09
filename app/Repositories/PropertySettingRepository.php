<?php

namespace App\Repositories;

use App\Models\PropertySetting;
use App\Contracts\PropertySettingRepositoryInterface;

class PropertySettingRepository implements PropertySettingRepositoryInterface
{
    /**
     * List of sensitive keys that should never be exposed via API.
     */
    protected array $sensitiveKeys = [
        'google_maps_api_key',
    ];

    /**
     * Get all property settings, excluding sensitive keys.
     */
    public function all()
    {
        return PropertySetting::whereNotIn('key', $this->sensitiveKeys)->get();
    }

    /**
     * Find a property setting by ID, excluding sensitive keys.
     */
    public function find($id)
    {
        $setting = PropertySetting::findOrFail($id);
        
        if (in_array($setting->key, $this->sensitiveKeys)) {
            abort(403, 'Access to this setting is forbidden');
        }

        return $setting;
    }

    /**
     * Get a setting by key without exposure restrictions (for internal use only).
     * Use with caution - never expose the result directly in API responses.
     */
    public function findByKey($key)
    {
        return PropertySetting::where('key', $key)->first();
    }

    /**
     * Create a new property setting.
     */
    public function create(array $data)
    {
        return PropertySetting::create($data);
    }

    /**
     * Update a property setting, excluding sensitive keys from being returned.
     */
    public function update($id, array $data)
    {
        $propertySetting = PropertySetting::findOrFail($id);
        
        if (in_array($propertySetting->key, $this->sensitiveKeys)) {
            abort(403, 'This setting cannot be modified via API');
        }

        $propertySetting->update($data);
        return $propertySetting;
    }

    /**
     * Delete a property setting, excluding sensitive keys.
     */
    public function delete($id)
    {
        $propertySetting = PropertySetting::findOrFail($id);
        
        if (in_array($propertySetting->key, $this->sensitiveKeys)) {
            abort(403, 'This setting cannot be deleted via API');
        }

        $propertySetting->delete();
        return true;
    }
}
