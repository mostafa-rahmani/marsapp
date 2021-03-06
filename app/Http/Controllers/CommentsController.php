<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Design;
use App\User;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Comment as CommentResource;
use App\Http\Resources\Design as DesignResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show(Request $request)
    {
        if ($comment = Comment::find( $request->comment )){
            $response =  [
                "status"    =>  "ok",
                "code"      =>  "200",
                "message"   => "comment returned successfully",
                "returned"  => "comment object",
                "data"      => [
                    "user"      => null,
                    "users"     => null,
                    "design"    => null,
                    "designs"    => null,
                    "comment"    => new CommentResource($comment),
                    "comments"   => null
                ]
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        }
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            "message"   => "Comment Not Found!",
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
        return response()->json($response, 404, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        if ($comment = Comment::find( $request->comment )){
            if ($request->user()->can('modify', $comment)) {
                try {
                    $this->validate($request, [
                        'content' => 'String',
                        'seen' => 'Boolean',
                    ]);
                } catch (ValidationException $e) {
                    $response =  [
                        "status"    =>  "error",
                        "code"      =>  "400",
                            "message"   => 'content and seen fields must not be empty. content must be String and seen must be Boolean',
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
                    return response()->json($response, 400, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                }
                $comment->update($request->only('content', 'seen'));
                    $response =  [
                        "status"    =>  "ok",
                        "code"      =>  "200",
                        "message"   => "comment updated successfully",
                        "returned"  => "the updated comment object",
                        "data"      => [
                            "user"      => new UserResource(User::find($request->user()->id)),
                            "users"     => null,
                            "design" =>    new DesignResource(Design::find($comment)),
                            "designs"    => null,
                            "comment"    => new CommentResource($comment),
                            "comments"   => null
                        ]
                    ];
                    return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

            }else{
                $response =  [
                    "status"    =>  "error",
                    "code"      =>  "403",
                    "message"   => "this comment does not belongs to you",
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
                return response()->json($response, 403, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            }
        }
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            "message"   => "Comment Not Found!",
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
        return response()->json($response, 404, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

    public function delete(Request $request)
    {
        if ($comment = Comment::find($request->comment)){
            if (request()->user()->can('modify', $comment)) {
                if ($comment->delete()) {
                    $response =  [
                        "status"    =>  "ok",
                        "code"      =>  "204",
                        "message"   => "something went wrong please try again",
                        "returned"  => 'auth',
                        "data"      => [
                            "user"      => null,
                            "users"     => null,

                            "design" => null,
                            "designs"    => null,

                            "comment"    => null,
                            "comments"   => null
                        ]
                    ];
                    return response()->json($response, 204, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                }
                $response =  [
                    "status"    =>  "error",
                    "code"      =>  "500",
                    "message"   => "something went wrong please try again",
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
                return response()->json($response, 500, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

            }
            $response =  [
                "status"    =>  "error",
                "code"      =>  "403",
                "message"   => "you are not allowed to delete this comment.",
                "returned"  => 'the design contained this comment',
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => Design::find($comment),
                    "designs"    => null,

                    "comment"    => null,
                    "comments"   => null
                ]
            ];
            return response()->json($response, 403, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        }
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            "message"   => "Comment Not Found",
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
        return response()->json($response, 404, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        /** @var TYPE_NAME $request */
        try {
            $this->validate($request, array(
                'content' => 'required|String'
            ));
        } catch (ValidationException $e) {
            $response =  [
                "status"    =>  "error",
                "code"      =>  "400",
                "message"   => 'the content of the comment is required and must be String.',
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
            return response()->json($response, 400, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        }
        if ($design = Design::find($request->design)){
            $comment = new Comment();
            $comment->user_id = $request->user()->id;
            $comment->design_id = $design->id;
            $comment->content = $request->input('content');
            if ($comment->save()){
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    "message"   => "comment created successfully",
                    "returned"  => "authenticated user, corresponding design, created comment",
                    "data"      => [
                        "user"      => new UserResource($request->user()),
                        "users"     => null,
                        "design" => new DesignResource(Design::find($design->id)),
                        "designs"    => null,
                        "comment"    => new CommentResource($comment),
                        "comments"   => null
                    ]
                ];
                return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            }
            $response =  [
                "status"    =>  "error",
                "code"      =>  "500",
                "message"   => "something went wrong please try again",
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
            return response()->json($response, 500, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        }
        // if Design Could not be found
        $response =  [
            "status"    =>  "error",
            "code"      =>  "404",
            "message"   => 'Design Not Found',
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
        return response()->json($response, 404, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }
}
