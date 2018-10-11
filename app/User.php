<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Design;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
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
}
