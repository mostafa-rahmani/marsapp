<?php

namespace App\Http\Controllers;

use App\Design;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\Design as DesignResource;
use App\Http\Resources\User as UserResource;
class SearchController extends Controller
{

    protected $results_perPage;
    public function __construct()
    {
        $this->results_perPage = 10;
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function search(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|String'
        ]);

        $user_result = User::search($request->input('query'))->get();
        $design_result = Design::search($request->input('query'))->get();

        $users = [];   $designs = [];
        foreach ($user_result as $item){
            array_push($users, new UserResrouce($item));
        }
        foreach ($design_result as $item){
            array_push($designs, new DesignResource($item));
        }
        $result = array_merge($users, $designs);
        return response($this->paginateAnswers($result, 20), 200);
    }



}
