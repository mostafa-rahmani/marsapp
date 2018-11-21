<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'app_download_url', 'landing_title', 'landing_description', 'admin_register_on',
        'about_first_text', 'about_second_text', 'about_first_img', 'about_second_img',
        'web_developer_img' , 'web_developer_url',
        'android_developer_img' , 'android_developer_url'

    ];
}
