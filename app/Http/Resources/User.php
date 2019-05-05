<?php

namespace App\Http\Resources;
use App\Http\Resources\DesignCollection;
use App\Http\Resources\Comment as CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
//        return parent::toArray($request);
        $download_count = 0;
        foreach ($this->designs as $design){
            $download_count = $download_count + $design->download_users()->count();
        }
        $arrObj = [
            "id"    => $this->id,
            "username" => $this->username,
            "email" => $this->email,
            "bio"   => $this->bio,
            "blocked"   => $this->blocked,
            "instagram" => $this->instagram,// rh.mostafa
            "instagram_url" => $this->instagram ?  'https://www.instagram.com/' . $this->instagram  : null,

            "profile_image" => $this->profile_image ? url('/') .
                Storage::url('public/' . $this->profile_image ) : null,
            "profile_background"  => $this->profile_background ? url('/') .
                Storage::url('public/' . $this->profile_background) : null,

            "download_count"    => $download_count,
            "likesCount"    => $this->likedDesigns()->count(),
            'designs' => new DesignCollection($this->designs),
            'following' => $this->following()->get(),
            'followers' => $this->followers()->get(),
            'liked_designs' => $this->likedDesigns()->get(),
            'downloads' =>  new DesignCollection($this->downloads()->get()),
            'seen_comments' => CommentResource::collection($this->seenComments()->get()),
            "comments"      => CommentResource::collection($this->comments()->get()),
            "created_at"    => $this->created_at->toDateTimeString(),
            "updated_at"    => $this->updated_at->toDateTimeString(),
        ];
        return $arrObj;
    }
}
