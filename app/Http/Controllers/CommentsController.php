<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Design;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show(Comment $comment)
    {
        return $this->commentOBJ($comment);
    }

    public function update(Request $request, Comment $comment)
    {
        if (request()->user()->can('modify', $comment)) {
            $this->validate($request, [
                'content' => 'String',
                'seen' => 'Boolean',
            ]);

            if ($comment->update($request->only('content', 'seen'))) {
                $response = [
                    'message' => 'comment was updated successfully ',
                    'comment' => $this->commentOBJ($comment)
                ];
                return response()->json($response, 200);
            }
            return response()->json(['message' => 'something went wrong please try again'], 500);
        }else{
            return response()->json(['message' => 'This comment does not belong to you'], 500);
        }
    }

    public function delete(Comment $comment)
    {
        if (request()->user()->can('modify', $comment)) {
            if ($comment->delete()){
                return response()->json(['message' => 'comment was successfully deleted'], 200);
            }
            return response()->json(['message' => 'something went wrong'], 404);
        }
        return response()->json(['message' => 'you are not allowed to delete this comment'], 401);
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
            $response = [
                'message' => 'comment was successfully created',
                'comment' => $this->commentOBJ($comment)
            ];
            return response()->json($response, 200);
        }
        return response()->json(['message' => 'something went wrong please try again'], 404);

    }
}
