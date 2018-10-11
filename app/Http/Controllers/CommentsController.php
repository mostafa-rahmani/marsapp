<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function show(Comment $comment)
    {
        return $comment;
    }

    public function update()
    {
        return 'updates a comment';
    }
    public function delete()
    {
        return 'deletes a comment';
    }
    public function store()
    {
        return 'stores a comment';
    }
}
