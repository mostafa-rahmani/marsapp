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
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $users = User::all();
        foreach ($users as $user){
            $this->userOBJ($user);
        }
        return $users;
    }

    public function show(User $user)
    {
        parent::userOBJ($user);
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
            $extension = $image->getClientOriginalExtension();
            $filename  = $this->profile_image_prefix . date('Y-m-d_h-m-s') .  "_{$user->id}_" . '.' . $extension;
            if ($this->storeImage($filename, $image, $this->profile_image_width)){
                if ($old_filename = $user->profile_image){
                    if ($this->deleteFile($old_filename)){
                        $data['profile_image']  =  $filename;
                    }
                }else{
                    $data['profile_image']  =  $filename;
                }
            }
        }
        if ($image = $request->file('profile_background')){
            // checking if profile background needs to be changed
            $extension = $image->getClientOriginalExtension();
            $filename  = $this->bg_image_prefix . date('Y-m-d_h-m-s') .  "_{$user->id}_" . '.' . $extension;
            if ($this->storeImage($filename, $image, $this->profile_bg_width )){// if saving was successful
                // now we delete the old one
                if ($old_filename = $user->profile_background){
                    if ($this->deleteFile($old_filename)){
                        $data['profile_background']  =  $filename;
                    }
                }else{
                    $data['profile_background']  =  $filename;
                }
            }
        }
        $user->update($data);
        return parent::userOBJ($user);
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
                abort(401);
            }
            $logged_in_user->following()->toggle($user->id);
            $response = [
                'followers' => $user->followers()->get(),
                'loged_in_user_followers' => $logged_in_user->following()->get()
            ];
            return response()->json($response, 201);

    }


    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse followings
     */
    public function followings(User $user)
    {
        $followings = $user->following()->get();
        return response()->json($followings, 200);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->get();
        return response()->json($followers, 200);
    }

    public function like(Request $request, Design $design)
    {
        $logged_in_user = $request->user();
        $design->likes()->toggle($logged_in_user->id);
        $response = [
            'design_likes' => $design->likes()->get(),
            'design_this_user_liked' => $logged_in_user->likedDesigns()->get()
        ];
        return response()->json($response, 200);
    }

//    protected function storeImage($filename, $image, $width){
//        $image = Image::make($image->getRealPath());
//        $image->widen($width, function ($constraint) {
//            $constraint->upsize();
//        });
//        return $image->save(storage_path('app/' . $this->user_image_folder . '/' . $filename)) ? true : false;
//    }
//
//    protected function deleteFile($filename){
//        $image = Storage::disk('local')->exists($this->user_image_folder . '/' . $filename);
//        if ($image){
//            return Storage::delete($this->user_image_folder . '/' . $filename);
//        }
//    }
}
