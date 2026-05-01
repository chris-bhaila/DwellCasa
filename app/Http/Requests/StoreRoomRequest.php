<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_type_id' => 'nullable|exists:room_types,id',
            'room_number' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('rooms', 'room_number')
                    ->where('location_id', $this->getLocationId()),
            ],
            'room_name' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:100',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'notes' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ];
    }

    private function getLocationId(): ?int
    {
        $user = auth()->user();
        return $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;
    }
}
