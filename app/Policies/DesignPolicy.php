<?php

namespace App\Policies;

use App\Design;
use App\User;
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
        if ($user->owns($design) && $user->blocked == 0){
            return true;
        }
        return false;
    }

    public function useApp(User $user)
    {
        return $user->blocked == 0;
    }

    public function download(User $user, Design $design)
    {
        return $design->is_download_allowed;
    }
}
