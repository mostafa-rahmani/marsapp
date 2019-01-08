<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Design;
use Illuminate\Http\Request;
use App\Http\Resources\Comment as CommentResource;


class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show(Comment $comment)
    {
        $response =  [
            "status"    =>  "ok",
            "code"      =>  "200",
            "message"   => "comment returned successfully",
            "returned"  => "comment object",
            "data"      => [
                "user"      => null,
                "users"     => null,

                "design" => null,
                "designs"    => null,

                "comment"    => $comment,
                "comments"   => null
            ]
        ];
        return response()->json($response, 200);
    }

    public function update(Request $request, Comment $comment)
    {
        if (request()->user()->can('modify', $comment)) {
            $this->validate($request, [
                'content' => 'String',
                'seen' => 'Boolean',
            ]);

            if ($comment->update($request->only('content', 'seen'))) {
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "200",
                    "message"   => "comment updated successfully",
                    "returned"  => "the updated comment object",
                    "data"      => [
                        "user"      => null,
                        "users"     => null,

                        "design" => null,
                        "designs"    => null,

                        "comment"    => $comment,
                        "comments"   => null
                    ]
                ];
                return response()->json($response, 200);
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
            return response()->json($response, 500);
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
            return response()->json($response, 403);
        }
    }

    public function delete(Comment $comment)
    {
        if (request()->user()->can('modify', $comment)) {

            if ($comment->delete()) {
                $response =  [
                    "status"    =>  "ok",
                    "code"      =>  "204",
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
                return response()->json($response, 204);
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
            return response()->json($response, 500);

        }
        $response =  [
            "status"    =>  "error",
            "code"      =>  "403",
            "message"   => "you are not allowed to delete this comment.",
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

    public function store(Request $request, Design $design)
    {
        $this->validate($request, [
            'content' => 'required|String'
        ]);
        $comment = new Comment();
        $comment->user_id = $request->user()->id;
        $comment->design_id = $design->id;
        $comment->content = $request->input('content');
        if ($comment->save()){
            $response =  [
                "status"    =>  "ok",
                "code"      =>  "200",
                "message"   => "comment created successfully",
                "returned"  => "the created comment object",
                "data"      => [
                    "user"      => null,
                    "users"     => null,

                    "design" => null,
                    "designs"    => null,

                    "comment"    => $comment->loadMissing("user"),
                    "comments"   => null
                ]
            ];
            return response()->json($response, 200);
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
        return response()->json($response, 500);

    }
}
