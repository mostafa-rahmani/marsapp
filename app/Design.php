<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


/**
 * @property mixed is_download_allowed
 * @property mixed blocked
 */
class Design extends Model
{
    use Searchable;
    protected $primaryKey = 'id';
    protected $perPage = 1;
    protected $table = 'designs';
    protected $with = ['user', 'comments', 'download_users', 'likes'];
    protected $appends = ['download_count', 'like_count'];
    protected $fillable = ['description', 'small_image', 'original_width', 'original_height', 'is_download_allowed', 'image', 'user_id'];

    public function getLikeCountAttribute()
    {
        return $this->attributes['like_count'] = $this->download_users()->count();
    }
    public function getDownloadCountAttribute()
    {
        return $this->attributes['download_count'] = $this->download_users()->count();
    }
    public function getSmallImageAttribute($value){
        return url()->to("\\") . Storage::url('public/' . 'sm_' . $this->image);
    }
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

    public function toSearchableArray()
    {
        return [
          'id' => $this->id,
          'username' => $this->username,
          'email' => $this->email,
          'bio' => $this->bio
        ];
    }
}
