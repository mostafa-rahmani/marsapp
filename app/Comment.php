<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Comment extends Model
{
    public function design()
    {
        return $this->belongsTo(Design::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
