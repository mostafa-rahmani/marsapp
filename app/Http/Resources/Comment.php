<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserInfo as UserInfo;
class Comment extends JsonResource
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
            "id" => $this->id,
            "content" => $this->content,
            "seen"  => $this->seen,
            "created_at"   => $this->created_at,
            "updated_at"    => $this->updated_at,
            "user_id"   => $this->user_id,
            "design_id" => $this->design_id,
            "user"  => new UserInfo($this->user),
            "design"    => "design"
        ];
    }
}
