<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;


class Design extends Model
{
    use Searchable;
    protected $primaryKey = 'id';
    protected $table = 'designs';

    protected $fillable = ['title', 'small_image', 'original_width', 'original_height', 'is_download_allowed', 'image', 'user_id'];

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withTimestamps();
    }

    public function download_users()
    {
        return $this->belongsToMany(User::class, 'downloads')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function searchableAs()
    {
        return 'design_index';
    }
}
