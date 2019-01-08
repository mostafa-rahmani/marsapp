<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed description
 * @property mixed image
 *  @property mixed small_image
 *  @property mixed original_width
 *  @property mixed original_height
 *  @property mixed is_download_allowed
 *  @property mixed blocked
 *  @property mixed created_at
 *  @property mixed updated_at
 *  @property mixed user_id
 *  @property mixed download_count
 *  @property mixed like_count
 *  @property mixed user
 *  @property mixed comments
 *  @property mixed download_users
 *  @property mixed likes
 */
class Design extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"                    =>  $this->id,
            "description"           =>  $this->description,
            "image"                 =>  $this->image,
            "small_image"           =>  $this->small_image,
            "original_width"        =>  $this->original_width,
            "original_height"       =>  $this->original_height,
            "is_download_allowed"   =>  $this->is_download_allowed,
            "blocked"               =>  $this->blocked,
            "user_id"               =>  $this->user_id,
            "user"                  =>  $this->user(),
            "download_count"        =>  $this->download_users()->count(),
            "like_count"            =>  $this->likes()->count(),
            "comments"              =>  $this->comments,
            "download_users"        =>  $this->download_users,
            "likes"                 =>  $this->likes()->get(),
            "created_at"            =>  $this->created_at->diffForHumans(),
            "updated_at"            =>  $this->updated_at->diffForHumans(),
        ];
    }
}
