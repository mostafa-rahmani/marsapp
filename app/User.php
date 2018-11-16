<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;
use Laravel\Passport\HasApiTokens;
use App\Role;
use App\User;

/**
 * @property int likesCount
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, Searchable;

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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
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
        return $this->id === $design->user()->id;
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

        return $this->blocked == "0";
    }
}
