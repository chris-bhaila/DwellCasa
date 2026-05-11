<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'front_page_sub_heading_1'  => 'nullable|string|max:255',
            'front_page_main_heading'   => 'nullable|string|max:255',
            'front_page_sub_heading_2'  => 'nullable|string|max:255',
            'front_page_end_heading'    => 'nullable|string|max:255',
            'front_page_end_sub_heading'=> 'nullable|string|max:255',
            'gallery_heading'           => 'nullable|string|max:255',
            'gallery_sub_heading'       => 'nullable|string|max:255',
            'reviews_sub_heading'       => 'nullable|string|max:255',
            'reviews_heading'           => 'nullable|string|max:255',
            'about_heading'             => 'nullable|string|max:255',
            'about_sub_description'     => 'nullable|string',
            'about_main_description'    => 'nullable|string',
            'contact_sub_heading'       => 'nullable|string|max:255',
            'contact_address'           => 'nullable|string|max:255',
            'contact_phone'             => 'nullable|array',
            'contact_phone.*'           => 'nullable|string|max:30',
            'contact_email'             => 'nullable|email|max:255',
            'check_in'                  => 'nullable|date_format:H:i',
            'check_out'                 => 'nullable|date_format:H:i',
            'facebook_link'             => 'nullable|url|max:255',
            'instagram_link'            => 'nullable|url|max:255',
            'footer_description'        => 'nullable|string',
            'homepage_main_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'homepage_end_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'about_image'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'map_lat'                   => 'nullable|numeric|between:-90,90',
            'map_lng'                   => 'nullable|numeric|between:-180,180',
        ];
    }
}
