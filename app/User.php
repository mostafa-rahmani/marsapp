<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int likesCount
 * @property string $profile_image_url
 * @property int $likes_count
 * @property string $background_image_url
 * @property int $download_count
 * @property null|string $instagram_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property mixed $attributes
 * @property mixed $comments
 * @property mixed $designs
 * @property int $id
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, Searchable;

    protected $perPage = 20;
    protected $primaryKey = 'id';
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password', 'username', 'profile_image', 'profile_background', 'instagram', 'email', 'bio'
    ];

    protected $appends = array('download_count', 'likesCount', 'instagram_url', 'profile_image_url', 'background_image_url');

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot', 'email_verified_at'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder){
            $builder->orderBy('created_at', 'desc');
        });
    }

    public function getDownloadCountAttribute(){

        $download_count = 0;
        foreach ($this->designs()->get() as $design)
            $download_count = $download_count + $design->download_users()->count();

        return $this->attributes['download_count'] = $download_count;

    }
    public function getLikesCountAttribute(){

        $likesCount = $this->likedDesigns()->count();
        return $this->attributes['likesCount'] = $likesCount;

    }

    public function getBackgroundImageUrlAttribute()
    {
        $image = $this->attributes['profile_background'];
        $url = $image ? url()->to("\\") . trim( Storage::url('public/' . $image), '/') : $image ;
        return $url;
    }

    public function getInstagramUrlAttribute()
    {
        return $this->attributes['instagram_url'] = $this->instagram ? 'http://www.instagram.com/' . $this->instagram : null ;
    }
    public function getProfileImageUrlAttribute()
    {
        $image = $this->attributes['profile_image'];
        $url = $image ? image_url($image, 'pi') : $image ;
        return $url;
    }
    // users that follow this user
    public function followers() {
        return $this->belongsToMany(User::class, 'follows',
            'following_id', 'follower_id')
            ->withTimestamps();
    }
    // users that are followed by this user
    public function following() {
        return $this->belongsToMany(User::class, 'follows',
            'follower_id', 'following_id')
            ->withTimestamps();
    }


    public function likedDesigns()
    {
        return $this->belongsToMany(Design::class, 'likes')
                    ->withTimestamps();
    }


    public function downloads()
    {
        return $this->belongsToMany(Design::class, 'downloads')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function designs(){
        return $this->hasMany(Design::class);
    }

    public function owns(Design $design)
    {
        return $this->id == $design->user()->id;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function isManager()
    {
        return $this->roles()->find(1) ? true : false;
    }
    public function isBlocked(){

        return $this->blocked == "1";
    }
    public function seenComments(){
        return $this->hasMany(Comment::class)->where('seen', '1');
    }
    public function searchableAs()
    {
        return 'User_index';
    }

   public function toSearchableArray()
   {
       $array = $this->only('bio', 'username', 'email');
       $array[$this->getKeyName()] = $this->getKey();
       return $array;
   }
}
