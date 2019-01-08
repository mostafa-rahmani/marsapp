<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;


class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
//        $users = User::with(
//            'seenComments', 'designs', 'following',
//            'followers', 'likedDesigns', 'comments')->get();
        $users = User::all();
        $response = [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "all users returned successfully",
            "data"      => [
                "user"      => null,
                "users"     => new UserCollection($users),

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
        $user = User::find($request->user);

        $response = [
            "status"    =>  $user ? "ok" : "error",
            "code"      =>  $user ? "200" : "404",
            "message"   => $user ? "user returned successfully" : "user not found",
            "returned"  => $user ? "the requested user object" : null,
            "data"      => [
                "user"      => new UserResource($user),
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
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
            if ($data['profile_image'] = store_user_image($image, 'profile_image')){
                if ($old_filename = $user->profile_image){
                     delete_image($old_filename , true); // name is contained prefix
                }
            }
        }
        if ($image = $request->file('profile_background')){
            if ($data['profile_background'] = store_user_image($image, 'profile_background')){
                if ($old_filename = $user->profile_background){
                    delete_image($old_filename, true);
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
                "user"      => new UserResource($user),
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
     * the authenticated user can follow another user but him\her self
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse user's followers
     */
    public function follow(Request $request)
    {
            if ( $subject_user = User::find($request->user) ){
                  $logged_in_user = $request->user();
                  if ($logged_in_user->id == $subject_user->id){
                    // in case user wants to follow/unfollow him/herself
                    $response = [
                      "status"    =>  "error",
                      "code"      =>  "422",
                      "message"   => "you can not follow your account",
                      "returned"  => null,
                      "data"      => [
                        "user"      => null,
                        "users"     => null,
                        "design"    => null,
                        "designs"    => null,
                        "comment"    => null,
                        "comments"   => null
                      ]
                    ];
                    return response()->json($response, 422);
                  }
                  if( $logged_in_user->following()->find($subject_user->id) ){
                    // if auth user is already following subject user
                      $response = [
                          "status"    =>  "ok",
                          "code"      =>  "200",
                          "message"   => "you followed ". $subject_user->username ." successfully ",
                          "returned"  => "auth user and followed user",
                          "data"      => [
                              "user"      => null,
                              "users"     => [
                                  new UserResource( User::find($request->user()->id) ),
                                  new UserResource( User::find($request->user) )
                              ],

                              "design"    => null,
                              "designs"    => null,

                              "comment"    => null,
                              "comments"   => null
                          ]
                      ];
                      return response()->json($response, 200);
                  }
                  // if everything was ok
                  $logged_in_user->following()->attach($subject_user->id);
                  $response = [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    "message"   => "you followed ". $subject_user->username ." successfully ",
                    "returned"  => "auth user and followed user",
                    "data"      => [
                      "user"      => null,
                      "users"     => [
                          new UserResource( User::find($request->user()->id) ),
                          new UserResource( User::find($request->user) )
                      ],

                      "design"    => null,
                      "designs"    => null,

                      "comment"    => null,
                      "comments"   => null
                      ]
                      ];
                  return response()->json($response, 200);
            }
            // if user could not be found
            $response = [
              "status"    =>  "error",
              "code"      =>  "404",
              "message"   => "user could not be found",
              "returned"  => null,
              "data"      => [

                "user"      => null,
                "users"     => null,
                "design"    => null,
                "designs"    => null,
                "comment"    => null,
                "comments"   => null

                ]
            ];
            return response()->json($response, 404);


    }

    /**
     *  authenticated user can  unfollow the users who had been followed by
     * @param Request $request
     * @return user object, Design object
     * */
    public function unfollow(Request $request)
    {
        if ( $subject_user = User::find($request->user) ){
            // if user was found
            $auth_user = $request->user();
            if ($auth_user->id == $subject_user->id) {
                  // if auth user is requesting for him/herself
                  $response = [
                    "status" => "error",
                    "code" => "422",
                    "message" => "you can not unfollow yourself",
                    "return" => null,
                    "data" => [
                      "user" => null,
                      "users" => null,
                      "design" => null,
                      "designs" => null,
                      "comment" => null,
                      "comments" => null


                    ]
                  ];
                  return response()->json($response, 422);
            }
            if( $auth_user->following()->find($request->user) ){
                  // if auth user is following subject user
                  $request->user()->following()->detach($subject_user);
                  $response = [
                    "status" => "ok",
                    "code" => "200",
                    "message" => "you successfully unfollowed $subject_user->username ",
                    "return" => "authenticated user and subject user",
                    "data" => [
                      "user" => null,
                      "users" => [
                          $request->user(),
                          User::find($request->user)
                      ],
                      "design" => null,
                      "designs" => null,
                      "comment" => null,
                      "comments" => null
                    ]
                  ];
                  return response()->json($response, 200);
            }
            // if auth user is not following subeject user then we just return the user and subject

            $response = [
                "status" => "ok",
                "code" => "200",
                "message" => "you successfully unfollowed $subject_user->username ",
                "return" => "authenticated user and subject user",
                "data" => [
                    "user" => null,
                    "users" => [
                        new UserResource( User::find($request->user()->id) ),
                        new UserResource( User::find($request->user) )
                    ],
                    "design" => null,
                    "designs" => null,
                    "comment" => null,
                    "comments" => null
                ]
            ];
            return response()->json($response, 200);

        }
        // if subject user could not be found in the Database
        $response = [
            "status" => "error",
            "code" => "404",
            "message" => "user could not be found",
            "return" => null,
            "data" => [
                "user" => null,
                "users" => null,
                "design" => null,
                "designs" => null,
                "comment" => null,
                "comments" => null
            ]
        ];
        return response()->json($response, 404);
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
            "message"   => "the following users returned successfully",
            "returned"  => "the following user objects of the given user",
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
            "message"   => "the follower users returned successfully",
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

}
