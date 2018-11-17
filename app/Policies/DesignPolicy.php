<?php

namespace App\Policies;

use App\Design;
use App\User;
use http\Env\Request;
use Illuminate\Auth\Access\HandlesAuthorization;

class DesignPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function modify(User $user, Design $design)
    {
        return $user->id == $design->user_id;
    }

    public function showDesign(User $user, Design $design)
    {
        //checking if this design is blocked by manager
        return $design->blocked == 0;
    }

    public function storeDesign(User $user)
    {
        return $user->isBlocked() ;
    }

    public function deleteDesign(User $user, Design $design)
    {
        return $user->id == $design->user_id;
    }

    public function download(User $user, Design $design)
    {
        return $design->is_download_allowed && $design->blocked == 0;
    }
}
