<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Comment extends Model
{
    protected $fillable = ['content', 'seen', 'design_id', 'user_id'];
    protected $with = ['user'];
    public function design()
    {
        return $this->belongsTo(Design::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
