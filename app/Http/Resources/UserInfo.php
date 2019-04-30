<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserInfo extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        $download_count = 0;
        foreach ($this->designs as $design){
            $download_count = $download_count + $design->download_users()->count();
        }

        $arrObj = [
            "id"    => $this->id,
            "username" => $this->username,
            "email" => $this->email,
            "profile_image" => $this->profile_image ? url('/') .
                Storage::url('public/' . $this->profile_image ) : null,
            "bio"   => $this->bio,
            "blocked"   => $this->blocked,
            "instagram" => $this->instagram,
            "profile_background"  => $this->profile_background ? url('/') .
                Storage::url('public/' . $this->profile_background) : null,
            "instagram_url" => $this->instagram ? 'https://www.instagram.com/' . $this->instagram  : null,
            "download_count"    => $download_count,
            "likesCount"    => $this->likedDesigns()->count(),
            "created_at"    => $this->created_at->toDateTimeString(),
            "updated_at"    => $this->updated_at->toDateTimeString(),
        ];
        return $arrObj;
    }
}
