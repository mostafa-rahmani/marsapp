<?php

namespace App\Http\Controllers;

use App\Design;
use App\Http\Requests\DesignRequest;
use function foo\func;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class DesignsController extends Controller
{
    public $lg_folder;
    public $lg_prefix;
    public $sm_folder;
    public $sm_prefix;
    public $thumbnail_width;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->lg_folder = 'full_size'; // storage path
        $this->sm_folder = 'public'; // storage path
        $this->lg_prefix = 'lg_';
        $this->sm_prefix = 'sm_';
        $this->thumbnail_width = 960;
    }


    public function index()
    {
        $designs = Design::where('blocked', '0')
                    ->paginate(20);
        return $designs;

    }


    public function show(Request $request)
    {
        $design = Design::find($request->design);
        if (Gate::allows('showDesign', $design)){ // checking blocked design
            $response =  [
                "status"    =>  "ok",
                "code"      =>  "200",
                "message"   => "design returned successfully",
                "returned"  => "requested design object",
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => $design,
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

    /**
     * downloads the full size image
     */
    public function download(Request $request, Design $design)
    {

        if (Gate::allows('download', $design)){
            $image = $design->image;
            $path = $this->imagePath($image, 'full');
            if($path){
                $request->user()->downloads()->detach($design->id);
                $request->user()->downloads()->attach($design->id);
                return Response::download($path);
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


    /**
     * creates a new design post
     * @param DesignRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DesignRequest $request)
    {
        $user = auth()->user();
        if ($user->isBlocked()){
            $image = $request->file('image');
            $data = $this->storeImage($image);
            $data['user_id'] = $user->id;
            $data['description'] = $request->description;
            $data['is_download_allowed'] = $request->is_download_allowed;
            $data['small_image'] = $this->imageUrl($data['image']);
            $design = Design::create($data);

            $response =  [
                "status"    =>  "ok",
                "code"      =>  "200",
                'message' => 'design successfully created',
                "returned"  => "the created design",
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => $design->loadMissing('user', 'comments', 'download_users', 'likes'),
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
            'message' => 'your account were blocked by the admins. you can not create a new design',
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


    /**
     * delete logged in user's design post
     * @param Design $design
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Design $design)
    {
            if (Gate::allows('deleteDesign', $design)){
                $this->deleteImage($design->image);
                $result = $design->delete();
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "204",
                    'message' => 'design was successfully deleted',
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
                return response()->json($response, 204);
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


    /**
     * updates logged in user's design postp
     * @param Request $request
     * @param Design $design
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Design $design)
    {
        if (Gate::allows('modify', $design)){
            $data = $request->only('image', 'is_download_allowed', 'description');
            if ($request->hasfile('image')){
                $image = $request->file('image');
                // we delete the old image
                if ($result = $this->deleteImage($design->image)){
                    $data = $this->storeImage($image);
                }else{
                    $response =  [
                        "status"    =>  "error",
                        "code"      =>  "500",
                        'message' => 'updating process was fail. try again',
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
                    return response()->json($response, 500);
                }
            }
            $design->update($data);

            $response =  [
                "status"    =>  "ok",
                "code"      =>  "200",
                'message' => 'design was updated successfully',
                "returned"  => "the updated design",
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => $design,
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
            'message' => 'you are not allowed to modify this design',
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


    /**
     * list of other users designs that logged in user is following theme
     * */
    public function followingDesigns(){
        $user = auth()->user();
        $followings = $user->following()->get();
        $designs = [];
        foreach ($followings as $user)
            foreach ($user->designs()->get() as $design)
                array_push($designs, $design);

        if ($designs){
            return response()->json($this->paginateAnswers($designs , 20), 200);
        }
       return response()->json($designs, 200);
    }

    public function list(Request $request)
    {

        $this->validate($request, [
            'ids' => 'Array|required'
        ]);
        $designs = Design::find($request->input('ids'));
        return response()->json($this->paginateAnswers($designs->toArray(), 20), 201);

    }
}
