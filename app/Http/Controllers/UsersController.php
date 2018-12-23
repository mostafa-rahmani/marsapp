<?php

namespace App\Http\Controllers;

use App\Design;
use App\Helpers\helpers;
use App\Helpers\marsHelper;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Symfony\Component\Console\Helper\Helper;

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

        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "all users returned successfully",
            "data"      => [
                "user"      => null,
                "users"     => $users->toArray(),

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response,200);
    }

    public function show(Request $request)
    {
        $user = User::with(
                    'seenComments', 'designs', 'following',
                    'followers', 'likedDesigns', 'comments'
                )->find($request->user);

        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "user returned successfully",
            "returned"  => "the requested user object",
            "data"      => [
                "user"      => $user,
                "users"     => null,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];

        return response()->json($response, 200);
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
            'profile_background' => 'image',
            'profile_image' => 'image'
        ]);

        $data = $request->only('username', 'instagram', 'bio' );
        if ($instagram = $request->instagram){
            $data['instagram'] =  $instagram;
        }
        $user = $request->user();
        if ($image = $request->file('profile_image')){
            if ($data['profile_image'] = $this->storeProfile($image, 'profile_image')){
                if ($old_filename = $user->profile_image){
                    $this->deleteImage ($old_filename , true); // name is contained prefix
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
        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "user updated successfully",
            "returned"  => "current logged in user",
            "data"      => [
                "user"      => $user->loadMissing('seenComments', 'designs', 'following', 'followers', 'likedDesigns', 'comments')->toArray(),
                "users"     => null,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 200);
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
                return response()->json(['message' => 'you can not follow your account', ], 403);
            }
            $logged_in_user->following()->toggle($user->id);

            $response = [
                "status"    =>  "ok",
                "code"      =>  "200",
                "message"   => "you followed ". $user->username ." successfully ",
                "returned"  => "current logged in user",
                "data"      => [
                    "user"      => $logged_in_user->loadMissing('seenComments', 'designs', 'following', 'followers', 'likedDesigns', 'comments'),
                    "users"     => null,

                    "design"    => null,
                    "designs"    => null,

                    "comment"    => null,
                    "comments"   => null
                ]
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
        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "following users returned successfully",
            "returned"  => "following user objects of the given user",
            "data"      => [
                "user"      => null,
                "users"     => $followings,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 200);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->with(
                            'seenComments', 'designs', 'following',
                            'followers', 'likedDesigns', 'comments')
                            ->get();
        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "follow users returned successfully",
            "returned"  => "the followers of the given user",
            "data"      => [
                "user"      => null,
                "users"     => $followers,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];

        return response()->json($response, 200);
    }

    public function like(Request $request, Design $design)
    {
        $logged_in_user = $request->user();
        $design->likes()->toggle($logged_in_user->id);

        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "you liked the design successfully",
            "returned"  => "current logged in user and the current liked design",
            "data"      => [
                "user"      => $logged_in_user->with(
                    'seenComments', 'designs', 'following',
                    'followers', 'likedDesigns', 'comments')
                    ->get(),
                "users"     => null,

                "design" => $design,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 200);
    }
}
