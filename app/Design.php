<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;


/**
 * @property mixed is_download_allowed
 * @property mixed blocked
 * @property mixed image
 * @property mixed user_id
 */
class Design extends Model
{
    use Searchable;
    protected $primaryKey = 'id';
    protected $perPage = 1;
    protected $table = 'designs';
    protected $hidden = ['pivot'];
//    protected $with = ['user', 'comments', 'download_users', 'likes'];
    protected $appends = ['download_count', 'like_count'];
    protected $fillable = ['description', 'small_image', 'original_width', 'original_height', 'is_download_allowed', 'image', 'user_id'];

    protected static function boot()
    {
        parent::boot();
        // Order by name ASC
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'Desc');
        });
    }

    public function getLikeCountAttribute()
    {
        return $this->attributes['like_count'] = $this->likes()->count();
    }
    public function getDownloadCountAttribute()
    {
        return $this->attributes['download_count'] = $this->download_users()->count();
    }
    public function getSmallImageAttribute($value){
        return url()->to("\\") . Storage::url('public/' . 'sm_' . $this->image);
        // return stripslashes($this->small_image);
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
        $array = $this->only('description');
        $array[$this->getKeyName()] = $this->getKey();
        return $array;
    }

}
