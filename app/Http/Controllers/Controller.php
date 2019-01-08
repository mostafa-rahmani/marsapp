<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $lg_folder = 'full_size';
    protected $lg_prefix = 'lg_';
    protected $sm_folder = 'public';
    protected $sm_prefix = 'sm_';
    protected $thumbnail_width = 100;

    protected $profile_image_prefix = 'public';
    protected $bg_image_prefix = 'profile_bg_';
    protected $profile_image_width = 100;
    protected $profile_bg_width = 960;
    protected $user_image_folder = 'public';
    protected $per_page = 20;

    /**
     * Paginate answers.
     *
     * @param array $answers
     *
     * @return LengthAwarePaginator
     */
    protected function paginateAnswers(array $answers, $perPage = 20)
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

