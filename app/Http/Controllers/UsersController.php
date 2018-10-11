<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UsersController extends Controller
{
    public function index()
    {
        return User::all();
    }
    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'username' => 'String|min:3',
            'profile_image_url'    => 'image',
            'bio' => 'String',
            'instagram' => 'String',
            'email' => 'email',
            'profile_background' => 'image',
        ]);
        return $user;
    }

    public function delete(User $user)
    {
        return $user;
    }
    public function follows()
    {
        return 'logged in user either follows or unfollows a user ';
    }

    public function likes()
    {
        return 'logged in user either likes or dislikes a user';
    }


}
