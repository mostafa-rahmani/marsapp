<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            "profile_image" => $this->profile_image ? image_url($this->profile_image, 'pi') : null,
            "profile_background"  => $this->profile_background ? image_url($this->profile_background, 'pg') : null,
            "seen_comments" => $this->seenComments()->get(),
            "designs"   => $this->designs()->get(),
            "following" => $this->following()->get(),
            "followers" => $this->followers()->get(),
            "download_count"    => $download_count,
            "liked_designs" => $this->likedDesigns()->get(),
            "likesCount"    => $this->likedDesigns()->count(),
            "downloads"     =>  $this->downloads()->get(),
            "comments"  => $this->comments()->get(),
            "created_at"    => $this->created_at->diffForHumans(),
            "updated_at"    => $this->updated_at->diffForHumans(),
        ];
    }
}
