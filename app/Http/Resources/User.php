<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property mixed blocked
 * @property mixed instagram
 * @property mixed profile_background
 * @property mixed profile_image
 */
class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $download_count = 0;
        foreach ($this->designs()->get() as $design)
            $download_count = $download_count + $design->download_users()->count();
        return [
            "id"    => $this->id,
            "username" => $this->username,
            "email" => $this->email,
            "bio"   => $this->bio,
            "blocked"   => $this->blocked,
            "instagram" => $this->instagram,
            "instagram_url" => $this->instagram ? 'https://www.instagram.com/' . $this->instagram  : null,
            "profile_image" => $this->profile_image ? url('/') . '/' .
                Storage::url('public/' . $this->profile_image ) : null,
            "profile_background"  => $this->profile_background ? url('/') . '/' .
                Storage::url('public/' . $this->profile_background) : null,
            "seen_comments" => $this->seenComments()->get(),
            "designs"   => $this->designs()->get(),
            "following" => $this->following()->get(),
            "followers" => $this->followers()->get(),
            "download_count"    => $download_count,
            "liked_designs" => $this->likedDesigns()->get(),
            "likesCount"    => $this->likedDesigns()->count(),
            "downloads"     =>  $this->downloads()->get(),
            "comments"  => $this->comments()->get(),
            "created_at"    => $this->created_at->toDateTimeString(),
            "updated_at"    => $this->updated_at->toDateTimeString(),
        ];
    }
}
