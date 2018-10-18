<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $full_size_path;
    protected $full_size_prefix;
    protected $small_size_path;
    protected $small_size_prefix;
    protected $thumbail_width;

    public function __construct()
    {
        $this->full_size_path = 'full_size'; // storage path
        $this->small_size_path = 'public'; // storage path
        $this->full_size_prefix = 'full_size_';
        $this->small_size_prefix = 'small_size_';
        $this->thumbail_width = 560;
    }

    protected function userOBJ($user){
        if (isset($user->profile_image)){
            $user->profile_image = url()->to("\\") . trim(Storage::url($this->user_image_folder . '/' . $user->profile_image), '/');
        }
        if (isset($user->profile_background)){
            $user->profile_background = url()->to("\\") . trim( Storage::url($this->user_image_folder . '/' . $user->profile_background), '/');
        }
        $user->designs = $user->designs()->get();
        $user->followigns = $user->following()->get();
        $user->followers = $user->followers()->get();
        $user->likesCount = $user->likedDesigns()->count();
        $user->liked_designs = $user->likedDesigns()->get();

        $download_count = 0;
        foreach ($user->designs()->get() as $design){
            $download_count = $download_count + $design->download_users()->count();
        }
        $user->download_count = $download_count;
        return $user;
    }

    protected function designOBJ($design){
        $design->comments = $design->comments()->get();
        $design->user = $design->user()->get();
        $design->small_image =  url()->to("\\") . trim(Storage::url( $this->small_size_prefix . $design->image), '/') ;
        $design->donload_count = $design->download_users()->count();
        $design->donload_users = $design->download_users()->get();
        $design->likes = $design->likes()->get();
        $design->like_count = $design->likes()->count();
        return $design;
    }
}

