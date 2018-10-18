<?php

namespace App\Http\Controllers;

use App\Design;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
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
        foreach ($user_result as $user){
            parent::userOBJ($user);
        }
        foreach ($design_result as $design){
            parent::designOBJ($design);
        }

        $users = [];   $designs = [];
        foreach ($user_result as $item){
            array_push($designs, $item);
        }
        foreach ($design_result as $item){
            array_push($users, $item);
        }
        $result = array_merge($users, $designs);
        return response($this->paginateAnswers($result, $this->results_perPage), 201);
    }


    /**
     * Paginate answers.
     *
     * @param array $answers
     *
     * @return LengthAwarePaginator
     */
    protected function paginateAnswers(array $answers, $perPage = 10)
    {
        $page = Input::get('page', 1);

        $offset = ($page * $perPage) - $perPage;

        $paginator = new LengthAwarePaginator(
            $this->transformAnswers($answers, $offset, $perPage),
            count($answers),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginator;
    }

    /**
     * Transform answers.
     *
     * @param array $answers
     * @param int $offset
     * @param int $perPage
     *
     * @return array
     */
    private function transformAnswers($answers, $offset, $perPage)
    {
        $answers = array_slice($answers, $offset, $perPage, true);

        return $answers;
    }
}
