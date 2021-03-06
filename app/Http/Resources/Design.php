<?php

namespace App\Http\Resources;
use App\Http\Resources\UserInfo;
use App\Http\Resources\Comment as CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        $result = [
            "id" => $this->id,
            "description" => $this->description,
            "image" => $this->image,
            "small_image" => url('/') . Storage::url('public/' . 'sm_' . $this->image),
            "original_width" => $this->original_width,
            "original_height" => $this->original_height,
            "is_download_allowed" => $this->is_download_allowed,
            "blocked" => $this->blocked,
            "user_id" => $this->user_id,
            "download_count" =>  $this->download_users()->count(),
            "like_count" => $this->likes()->count(),
            "user" => new UserInfo($this->user),
            "comments" => CommentResource::collection($this->comments),
            "download_users" => UserInfo::collection($this->download_users),
            "likes" => UserInfo::collection($this->likes()->get()),
            "created_at" => $this->created_at->toDateTimeString(),
            "updated_at" => $this->updated_at->toDateTimeString(),
        ];
        return $result;
    }
}
