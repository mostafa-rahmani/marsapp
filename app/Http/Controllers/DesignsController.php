<?php

namespace App\Http\Controllers;

use App\Design;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Design as DesignResource;
use App\Http\Resources\DesignCollection;
use App\Http\Requests\DesignRequest;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class DesignsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index()
    {
        return new DesignCollection(Design::where('blocked', '0')
        ->paginate(20));
    }

    public function show(Request $request)
    {
        if ($design = Design::find($request->design)) {
            // if design was found
            if (Gate::allows('showDesign', $design)){ // checking blocked design
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    "message"   => "design returned successfully",
                    "returned"  => "requested design object",
                    "data"      => [
                        "user"      => null,
                        "users"     => null,
                        "design" => new DesignResource($design),
                        "designs"    => null,
                        "comment"    => null,
                        "comments"   => null
                    ]
                ];
                return response()->json($response, 200);
            }
            $response =  [
                "status"    =>  "error",
                "code"      =>  "403",
                "message"   => "you can not access this design. it is blocked by the admins.",
                "returned"  => null,
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => null,
                    "designs"    => null,

                    "comment"    => null,
                    "comments"   => null
                ]
            ];
            return response()->json($response, 403);
        }
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            "message"   => "design not found",
            "returned"  => null,
            "data"      => [
                "user"      => null,
                "users"     => null,
                "design" => null,
                "designs"    => null,
                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 404);

    }

    /**
     * downloads the full size image
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request)
    {
        if ( $design = Design::find($request->design) ) {
            if (Gate::allows('download', $design)){
                $image = $design->image;
                $path = image_path($image, 'full');
                if($path){
                    if ($request->user()->downloads()->get()->find($design)){
                        return Response::download($path);
                    }else{
                        $request->user()->downloads()->attach($request->design);
                        return Response::download($path);
                    }
                }
                $response =  [
                    "status"    =>  "error",
                    "code"      =>  "404",
                    "message" => "cant find the file to download. please try later",
                    "returned"  => null,
                    "data"      => [
                        "user"      => null,
                        "users"     => null,

                        "design" => null,
                        "designs"    => null,

                        "comment"    => null,
                        "comments"   => null
                    ]
                ];
                return response()->json($response, 404);

            } else {
                $response =  [
                    "status"    =>  "error",
                    "code"      =>  "403",
                    'message' => 'this design is not allowed to be downloaded',
                    "returned"  => null,
                    "data"      => [
                        "user"      => null,
                        "users"     => null,

                        "design" => null,
                        "designs"    => null,

                        "comment"    => null,
                        "comments"   => null
                    ]
                ];
                return response()->json($response, 403);
            }
        }
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            'message' => 'design could not be found.',
            "returned"  => null,
            "data"      => [
                "user"      => null,
                "users"     => null,

                "design" => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response , 404);
    }

    /**
     * creates a new design post
     * @param DesignRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DesignRequest $request)
    {
        $user = $request->user();
        if (!$user->isBlocked()){
            $image = $request->file('image');
            $data = store_design_image($image);
            $data['user_id'] = $user->id;
            $data['description'] = $request->description;
            $data['is_download_allowed'] = $request->is_download_allowed;
            $data['small_image'] = image_url($data['image'], 'sm');
            $design = Design::create($data);

            $response =  [
                "status"    =>  "ok",
                "code"      =>  "200",
                'message' => 'design successfully created',
                "returned"  => "auth user, created design",
                "data"      => [
                    "user"      => new UserResource(User::find($request->user()->id)),
                    "users"     => null,

                    "design" => new DesignResource(Design::find($design->id)),
                    "designs"    => null,

                    "comment"    => null,
                    "comments"   => null
                ]
            ];
            return response()->json($response, 200);
        }
        $response =  array(
            "status"    =>  "error",
            "code"      =>  "403",
            'message' => 'your account was blocked by the admins. you can not create a new design',
            "returned"  => null,
            "data"      => array(
                "user"      => null,
                "users"     => null,

                "design" => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            )
        );
        return response()->json($response, 403);
    }

    /**
     * delete logged in user's design post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if( $design = Design::find($request->design) ){
            if (Gate::allows('deleteDesign', $design)){
                delete_image($design->image);
                $design->delete();
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    'message' => 'design was successfully deleted',
                    "returned"  => 'auth user',
                    "data"      => [
                        "user"      => new UserResource(User::find($request->user()->id)),
                        "users"     => null, "design" => null, "designs" => null, "comment"    => null,  "comments"   => null
                    ]
                ];
                return response()->json($response, 200);
            }
            $response =  [
                "status"    =>  "error",
                "code"      =>  "403",
                'message' => 'this design does not belong to you.',
                "returned"  => null,
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => null,
                    "designs"    => null,

                    "comment"    => null,
                    "comments"   => null
                ]
            ];
            return response()->json($response, 403);
        }
        // when design could not be found
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            'message' => 'Design Could Not Be Found',
            "returned"  => null,
            "data"      => [
                "user"      => null,
                "users"     => null,
                "design" => null,
                "designs"    => null,
                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 404);
    }

    /**
     * updates logged in user's design postp
     * @param Request $request
     * @param Design $design
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            "is_download_allowed" => "Boolean",
            "image"               => "Image",
            "description"         => "String"
        ]);
        if( $design = Design::find($request->design)){
            if ( Gate::allows('modify', $design) ){
                $data = $request->only( 'is_download_allowed', 'description');
                if ($request->hasfile('image')){
                    $image = $request->file('image');
                    // we delete the old image
                    if ($result = delete_image($design->image)){
                        $save_result = store_design_image($image);
                        $data = array_merge($data, $save_result);
                    }else{
                        $response =  [
                            "status"    =>  "error",
                            "code"      =>  "500",
                            'message' => 'updating process was failed. try again',
                            "returned"  => null,
                            "data"      => [
                                "user"      => null, "users"     => null,
                                "design" => null, "designs"    => null,
                                "comment"    => null, "comments"   => null
                            ]
                        ];
                        return response()->json($response, 500);
                    }
                }
                $design->update($data);
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    'message' => 'design was updated successfully',
                    "returned"  => "authenticated user, updated design",
                    "data"      => [
                        "user"      => new UserResource($request->user()),
                        "users"     => null,
                        "design" => new DesignResource(Design::find($design->id)),
                        "designs"    => null,
                        "comment"    => null,
                        "comments"   => null
                    ]
                ];
                return response()->json($response, 200);
            }
            $response =  [
                "status"    =>  "error",
                "code"      =>  "403",
                'message' => 'you are not allowed to modify this design.',
                "returned"  => null,
                "data"      => [
                    "user"      => null,
                    "users"     => null,
                    "design" => null,
                    "designs"    => null,
                    "comment"    => null,
                    "comments"   => null
                ]
            ];
            return response()->json($response, 403);
        }
        // when design could not be found
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            'message' => 'Design Not Found',
            "returned"  => null,
            "data"      => [
                "user"      => null,
                "users"     => null,
                "design" => null,
                "designs"    => null,
                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 404);

    }

    /**
     * list of other users designs that logged in user is following theme
     * */
    public function followingDesigns()
    {
        $user = auth()->user();
        $followings = $user->following()->get();
        $designs = [];
        foreach ($followings as $user)
            foreach ($user->designs()->get() as $design)
                array_push($designs, $design);

        return response()->json($this->paginateAnswers(new DesignCollection($designs), 20), 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function list(Request $request)
    {

        $this->validate($request, [
            'ids' => 'Array|required'
        ]);
        return  new DesignCollection( Design::whereIn('id', $request->input('ids'))->paginate(20) );
    }

    /**
     * like design method
     * @params design id
     * @param Request $request
     * @return auth user object and liked design
     */
    public function likeDesign( Request $request )
    {
        if ( $design = Design::find( $request->design ) ){
            if ( $design->likes()->find($request->user()->id) ){
                $response = [
                    "status" => "ok",
                    "code" => "200",
                    "message"   => "you successfully liked design " . $design->id,
                    "returned" => "liked design and authenticated user",
                    "data"  => [
                        "user" => new UserResource(User::find($request->user()->id)),
                        "users" => "",
                        "design" => new DesignResource(Design::find($request->design)),
                        "designs" => "",
                        "comment" => "",
                        "comments" => ""
                    ]
                ];
                return response()->json($response, 200);
            }
            $design->likes()->attach($request->user());
            $response = [
                "status" => "ok",
                "code" => "200",
                "message"   => "you successfully liked design " . $design->id,
                "returned" => "liked design and authenticated user",
                "data"  => [
                    "user" => new UserResource(User::find($request->user()->id)),
                    "users" => "",
                    "design" => new DesignResource(Design::find($request->design)),
                    "designs" => "",
                    "comment" => "",
                    "comments" => ""
                ]
            ];
            return response()->json($response, 200);
        }
        $response = [
            "status" => "error",
            "code" => "404",
            "message"   => "Design Could Not Be Found",
            "returned" => null,
            "data"  => [
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
     * dislike design method
     * @params design id
     * @param Request $request
     * @return auth user object and disliked design
     */
    public function dislikeDesign(Request $request)
    {
        if ( $design = Design::find($request->design ) ){
            if ( $design->likes()->find($request->user()->id) ){
                $design->likes()->detach($request->user());
                $response = [
                "status" => "ok",
                "code" => "200",
                "message"   => "You successfully disliked design " . $design->id,
                "returned" => "disliked design and authenticated user",
                "data"  => [
                    "user" => new UserResource(User::find($request->user()->id)),
                    "users" => "",
                    "design" => new DesignResource(Design::find($request->design)),
                    "designs" => "",
                    "comment" => "",
                    "comments" => ""
                ]
                ];
                return response()->json($response, 200);
            }
            $response = [
            "status" => "ok",
            "code" => "200",
            "message"   => "You successfully disliked design " . $design->id,
            "returned" => "disliked design and authenticated user",
            "data"  => [
                "user" => new UserResource(User::find($request->user()->id)),
                "users" => "",
                "design" => new DesignResource(Design::find($request->design)),
                "designs" => "",
                "comment" => "",
                "comments" => ""
            ]
        ];
            return response()->json($response, 200);
        }
        $response = [
            "status" => "error",
            "code" => "404",
            "message"   => "Design Could Not Found",
            "returned" => null,
            "data"  => [
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

}
