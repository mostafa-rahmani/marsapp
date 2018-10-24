<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['app_download_url', 'landing_title', 'landing_description', 'admin_register_on'];
}
