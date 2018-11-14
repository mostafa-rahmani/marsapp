<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Design;
use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $full_size_path;
    protected $full_size_prefix;
    protected $small_size_path;
    protected $small_size_prefix;
    protected $thumbnail_width;
    //users
    protected $profile_image_prefix;
    protected $bg_image_prefix;
    protected $profile_image_width;
    protected $profile_bg_width;
    protected $user_image_folder;
    protected $per_page;
    public function __construct()
    {
        $this->full_size_path = 'full_size'; // storage path
        $this->small_size_path = 'public'; // storage path
        $this->full_size_prefix = 'full_size_';
        $this->small_size_prefix = 'small_size_';
        $this->thumbnail_width = 560;
        //users
        $this->user_image_folder     = 'public';
        $this->bg_image_prefix       = 'profile_bg_';
        $this->profile_image_prefix  = 'profile_image_';
        $this->profile_image_width   = 100;
        $this->profile_bg_width      = 900;
        $this->per_page = 20;
    }

    protected function userOBJ(User $user, $withDesign = true){
        if (isset($user->profile_image)){
            $user->profile_image = url()->to("\\") . trim(Storage::url($this->user_image_folder . '/' . $user->profile_image), '/');
        }
        if (isset($user->profile_background)){
            $user->profile_background = url()->to("\\") . trim( Storage::url($this->user_image_folder . '/' . $user->profile_background), '/');
        }

        if ($withDesign){
            $designs = $user->designs()->get();
            foreach ($designs as $design){
                $this->designOBJ($design, false);
            }
            $user->designs = $designs;
        }
        $user->followigns = $user->following()->get();
        $user->followers = $user->followers()->get();
        $user->likesCount = $user->likedDesigns()->count();
        $user->liked_designs = $user->likedDesigns()->get();
        $seenComments = $user->comments()->where('seen', 1)->get();
        foreach ($seenComments as $comment){
            $this->commentOBJ($comment, false, false);
        }
        $user->seenComments = $seenComments;

        $download_count = 0;
        foreach ($user->designs()->get() as $design){
            $download_count = $download_count + $design->download_users()->count();
        }
        $user->download_count = $download_count;

        return $user;
    }

    protected function designOBJ(Design $design, $withUser = true, $withComments = true){

        if ($withComments){
            foreach($design->comments() as $comment){
                $this->commentOBJ($comment , true, false);
            };
        }
        if ($withUser){
            $user = $design->user()->first();
            $design->user = $this->userOBJ($user, false );
        }
        $design->small_image =  url()->to("\\") . trim(Storage::url( $this->small_size_prefix . $design->image), '/') ;
        $design->donload_count = $design->download_users()->count();
        $design->donload_users = $design->download_users()->get();
        $design->likes = $design->likes()->get();
        $design->like_count = $design->likes()->count();
        return $design;
    }

    protected function commentOBJ(Comment $comment, $withUser = true, $withDesign = true){
        if ($withUser ){
            $comment->user = $this->userOBJ($comment->user, false);
        }
        if ($withDesign){
            $comment->design = $this->designOBJ($comment->design , true, false);
        }
        return $comment;
    }

    protected function deleteImage($imageName){
        $full_image = Storage::disk('local')->exists($this->full_size_path . '/' . $this->full_size_prefix . $imageName);
        $small_image = Storage::disk('local')->exists($this->small_size_path . '/' . $this->small_size_prefix . $imageName);
        if ($small_image && $full_image){
            return Storage::delete([
                $this->full_size_path . '/' . $this->full_size_prefix . $imageName,
                $this->small_size_path . '/' . $this->small_size_prefix . $imageName
            ]);
        }
        // file does exist
        return false;
    }

    /**
     * stores full size image and small size
     * @param image
     */
    protected function storeImage($image){
        $extension = $image->getClientOriginalExtension();
        // give it a name // we cant use image name maybe it contains sql
        $filename = date('Y-m-d_h-m-s') . '_' . str_random('4') . '.' . $extension;
        $image->storeAs( $this->full_size_path,  $this->full_size_prefix . $filename);
        $image = Image::make($image->getRealPath());
        $data['original_width']  =  $image->width();
        $data['original_height'] = $image->height();
        if ($data['original_width'] > $this->thumbnail_width){
            $image->widen($this->thumbnail_width, function ($constraint) {
                $constraint->upsize();
            });
            $image->save(storage_path('app/' . $this->small_size_path . '/' . $this->small_size_prefix . $filename));
        }else{
            $image->save(storage_path('app/' . $this->small_size_path . '/' .$this->small_size_prefix . $filename));
        }

        $data['image'] = $filename;
        return $data;
    }

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

