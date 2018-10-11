<?php

namespace App\Http\Controllers;

use App\Design;
use Illuminate\Http\Request;

class DesignsController extends Controller
{
    public function index()
    {
        return Design::all();
    }

    public function show(Design $design)
    {
        return $design;
    }

    /*
     * creates a new design post
     * */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:5',
            ''
        ]);
    }
    /*
     * delete logged in user's design post
     * */
    public function delete()
    {
        return ' delete logged in user\'s design post';
    }
    /*
     * updates logged in user's design post
     * */
    public function update()
    {
        return 'updates logged in user\'s design post';
    }
    /*
     * this method gets culled whenever user downloads a design
     * */
    public function download()
    {
        return 'updates logged in user\'s design post';
    }
    /*
     * list of other users designs that logged in user is following theme
     * */
    public function followingDesigns(){
        return 'list of other users designs that logged in user is following theme';
    }
}
