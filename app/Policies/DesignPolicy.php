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
        return $user->owns($design);
    }

    public function download(User $user, Design $design)
    {
        return $design->is_download_allowed;
    }
}
