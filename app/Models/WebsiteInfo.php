<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteInfo extends Model
{
    use HasFactory;

    protected $table = 'website_info';

    protected $fillable = [
        'front_page_sub_heading_1',
        'front_page_main_heading',
        'front_page_sub_heading_2',
        'front_page_end_heading',
        'front_page_end_sub_heading',
        'gallery_heading',
        'gallery_sub_heading',
        'about_heading',
        'about_sub_description',
        'about_main_description',
        'contact_sub_heading',
        'contact_address',
        'contact_phone',
        'contact_email',
        'check_in',
        'check_out',
        'facebook_link',
        'instagram_link',
        'footer_description',
        'homepage_main_image',
        'homepage_end_image',
        'about_image',
    ];

    protected $casts = [
        'check_in'  => 'string',
        'check_out' => 'string',
    ];
}