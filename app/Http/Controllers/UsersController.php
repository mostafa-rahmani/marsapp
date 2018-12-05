<?php

namespace App\Http\Controllers;

use App\Design;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UsersController extends Controller
{
    public $profile_image_prefix;
    public $bg_image_prefix;
    public $profile_image_width;
    public $profile_bg_width;
    public $user_image_folder;
    public $per_page;

    public function __construct()
    {
        $this->middleware('auth:api');
        //users
        $this->user_image_folder     = 'public';
        $this->bg_image_prefix       = 'profile_bg_';
        $this->profile_image_prefix  = 'profile_image_';
        $this->profile_image_width   = 100;
        $this->profile_bg_width      = 900;
        $this->per_page = 20;
    }

    public function index()
    {
        $users = User::with(
                'seenComments', 'designs', 'following',
                        'followers', 'likedDesigns', 'comments')->get();
        return $users;
    }

    public function show(Request $request)
    {
        $user = User::with(
                'seenComments', 'designs', 'following',
                'followers', 'likedDesigns', 'comments'
                )->find($request->user);
        return response()->json($user, 201);
    }

    /**
     * it updates any user info
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'username' => 'String|unique:users',
            'instagram' => 'String',
            'bio' => 'String',
            'email' => 'email',
            'profile_background' => 'image',
            'profile_image' => 'image'
        ]);

        $data = $request->only('username', 'instagram', 'email', 'bio' );
        if ($instagram = $request->instagram){
            $data['instagram'] =  $instagram;
        }
        $user = $request->user();
        if ($image = $request->file('profile_image')){
            if ($data['profile_image'] = $this->storeProfile($image, 'profile_image')){
                if ($old_filename = $user->profile_image){
                    $this->deleteImage ($old_filename , true); // name has already contained prefix
                }
            }
        }
        if ($image = $request->file('profile_background')){
            if ($data['profile_background'] = $this->storeProfile($image, 'profile_background')){
                if ($old_filename = $user->profile_background){
                    $this->deleteImage($old_filename, true);
                }
            }
        }
        $user->update($data);
        return response()->json($user->loadMissing('seenComments', 'designs', 'following', 'followers', 'likedDesigns', 'comments'), 200);
    }

    /**
     * the logged in user either follows or unfollows a user
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse user's followers
     */
    public function follow(Request $request, User $user)
    {
            $logged_in_user = $request->user();
            if ($logged_in_user->id == $user->id){// in case user wants to follow/unfollow himself
                return response()->json(['message' => 'you can not follow your acount', ], 403);
            }
            $logged_in_user->following()->toggle($user->id);


            $followings = $logged_in_user->following()
                                    ->with('seenComments', 'designs', 'following', 'followers', 'likedDesigns', 'comments')
                                    ->get();
            $followers = $logged_in_user->followers()
                                        ->with('seenComments', 'designs', 'following', 'followers', 'likedDesigns', 'comments')
                                        ->get();
            $response = [
                'followers' => $followers,
                'loged_in_user_followings' => $followings
            ];
            return response()->json($response, 201);

    }


    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse followings
     */
    public function followings(User $user)
    {
        $followings = $user->following()
                            ->with(
                            'seenComments', 'designs', 'following',
                                    'followers', 'likedDesigns', 'comments')
                            ->get();
        return response()->json($followings, 200);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->with(
                            'seenComments', 'designs', 'following',
                            'followers', 'likedDesigns', 'comments')
                            ->get();
        return response()->json($followers, 200);
    }

    public function like(Request $request, Design $design)
    {
        $logged_in_user = $request->user();
        $design->likes()->toggle($logged_in_user->id);
        $design_likes = $design->likes()->with(
                    'seenComments', 'designs', 'following',
                    'followers', 'likedDesigns', 'comments')
                    ->get();

        $liked_Designs =  $logged_in_user->likedDesigns()->get();
        $response = [
            'design_likes' => $design_likes,
            'design_this_user_liked' => $liked_Designs
        ];
        return response()->json($response, 200);
    }
}
