<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


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
        if ($user = User::find($request->user)){
            $response = [
                "status"    =>  "ok",
                "code"      =>  "200",
                "message"   => "user returned successfully",
                "returned"  => "the requested user object",
                "data"      => [
                    "user"      => new UserResource($user),
                    "users"     => null,
                    "design"    => null,
                    "designs"    => null,
                    "comment"    => null,
                    "comments"   => null
                ]
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        }
        $response = [
            "status"    =>  "error",
            "code"      =>  "404",
            "message"   => 'User Not Found',
            "returned"  => null,
            "data"      => [
                "user"      => new UserResource($user),
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
            try{
                $data['profile_image'] = store_user_image($image, 'profile_image');
            }catch (ValidationException $e){
                $response = [
                    "status"    =>  "error",
                    "code"      =>  $e->status,
                    "message"   => "error when saving file. try again or fix this.",
                    "returned"  => null,
                    "data"      => [
                        "user"      => null,
                        "users"     => null,
                        "design"    => null,
                        "designs"    => null,
                        "comment"    => null,
                        "comments"   => null,
                    ]
                ];
                return response()->json($response, 500);
            }
            if ($data['profile_image']){
                if ($old_filename = $user->profile_image){
                    delete_image($old_filename , true); // name is contained prefix
                }
            }
        }
        if ($image = $request->file('profile_background')){
            if ($data['profile_background'] = store_user_image($image, 'profile_background')){
                if ($old_filename = $user->profile_background){
                    delete_image($old_filename, false);
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
                "user"      => new UserResource(User::find($request->user()->id)),
                "users"     => null,

                "design"    => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
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
                      return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                  }
                  // if everything was ok
                  $logged_in_user->following()->attach($subject_user->id);
                  $response = [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    "message"   => "you followed ". $subject_user->username ." successfully ",
                    "returned"  => "auth user and followed user",
                    "data"      => [
                      "user"      => new UserResource( User::find($request->user) ),
                      "users"     => [
                          new UserResource( User::find($request->user()->id) )
                      ],
                      "design"    => null,
                      "designs"    => null,
                      "comment"    => null,
                      "comments"   => null
                      ]
                      ];
                  return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
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
                      "user" => new UserResource(User::find($request->user)),
                      "users" => [
                          new UserResource(User::find($request->user()->id))
                      ],
                      "design" => null,
                      "designs" => null,
                      "comment" => null,
                      "comments" => null
                    ]
                  ];
                  return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            }
            // if auth user is not following subeject user then we just return the user and subject
            $response = [
                "status" => "ok",
                "code" => "200",
                "message" => "you successfully unfollowed $subject_user->username ",
                "return" => "authenticated user and subject user",
                "data" => [
                    "user" => new UserResource( User::find($request->user()->id) ),
                    "users" => [
                        new UserResource( User::find($request->user) )
                    ],
                    "design" => null,
                    "designs" => null,
                    "comment" => null,
                    "comments" => null
                ]
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
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
        $followings = new UserCollection($user->following()->get());
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
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

    public function followers(User $user)
    {
        $followers = new UserCollection($user->followers()->get());
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

        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

}
