<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Comment extends Model
{
    protected $fillable = ['content', 'design_id', 'user_id'];
    public function design()
    {
        return $this->belongsTo(Design::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
