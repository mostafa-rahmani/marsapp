<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function modify(User $user, Comment $comment)
    {
        return $user->id == $comment->user_id;
    }
}
