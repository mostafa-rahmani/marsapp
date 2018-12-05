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
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $lg_folder;
    public $lg_prefix;
    public $sm_folder;
    public $sm_prefix;
    public $thumbnail_width;

    public $profile_image_prefix;
    public $bg_image_prefix;
    public $profile_image_width;
    public $profile_bg_width;
    public $user_image_folder;
    public $per_page;
    public function __construct()
    {
        $this->lg_folder = 'full_size'; // storage path
        $this->sm_folder = 'public'; // storage path
        $this->lg_prefix = 'lg_';
        $this->sm_prefix = 'sm_';
        $this->thumbnail_width = 960;
        //users
        $this->user_image_folder     = 'public';
        $this->bg_image_prefix       = 'profile_bg_';
        $this->profile_image_prefix  = 'profile_image_';
        $this->profile_image_width   = 100;
        $this->profile_bg_width      = 900;
        $this->per_page = 20;
    }








    /**
     * @param $imageName
     * @return bool true if deletion was successful
     * @return bool false if there is no existing file
     */
    protected function deleteImage($imageName , $profile = false){
        if ($profile &&
            file_exists(storage_path('app/' . $this->user_image_folder .  '/' . $imageName))){
            $profile = Storage::delete( $this->user_image_folder . '/'  . $imageName);
            return $profile ? true : false;
        }
        if (file_exists(storage_path('app/' . $this->sm_folder . '/' . $this->sm_prefix . $imageName))){
            $sm = Storage::delete( $this->sm_folder . '/' . $this->sm_prefix . $imageName);
        }
        if (file_exists(storage_path('app/' . $this->lg_folder . '/' . $this->lg_prefix . $imageName))){
            $lg = Storage::delete( $this->lg_folder . '/' . $this->lg_prefix . $imageName);
            return isset($sm) && $sm && $lg ?  true : false;
        }
    }

    /**
     * stores full size image and small size
     * @param image
     */
    protected function storeImage($image){
        $extension = $image->getClientOriginalExtension();
        $filename = date('Y-m-d_h-m-s') . '_' . str_random('4') . '.' . $extension;
        $fullSizeName = $this->lg_prefix . $filename;
        $smallSizeName = $this->sm_prefix . $filename;
        $image = Image::make($image->getRealPath());
        $image->save(storage_path( 'app/' . $this->lg_folder . '/' . $fullSizeName));
        $data['original_width']  =  $image->getWidth();
        $data['original_height'] = $image->getHeight();
        if ($data['original_width'] > $this->thumbnail_width){
            $image->widen($this->thumbnail_width,  function ($constraint) {
                $constraint->upsize();
            });
            $image->save(storage_path('app/' . $this->sm_folder . '/' . $smallSizeName));
        }else{
            $image->save(storage_path('app/' . $this->sm_folder . '/' . $smallSizeName));
        }

        $data['image'] = $filename;
        return $data;
    }

    protected function storeProfile($image, $type){
        $extension = $image->getClientOriginalExtension();
        $user = auth()->user();
        if ($type == 'profile_background'){
            $filename  = $this->bg_image_prefix. date('Y-m-d_h-m-s') .  "_{$user->id}_" . '.' . $extension;
            $image = Image::make($image->getRealPath());
            $image->widen($this->profile_bg_width,  function ($constraint) {
                $constraint->upsize();
            });
            $image->save(storage_path('app/' . $this->user_image_folder . '/' . $filename));
            return $filename;
        }
        if ($type == 'profile_image'){
            $filename  = $this->profile_image_prefix . date('Y-m-d_h-m-s') .  "_{$user->id}_" . '.' . $extension;
            $image = Image::make($image->getRealPath());
            $image->widen($this->profile_image_width,   function ($constraint) {
                $constraint->upsize();
            });// needs to be square
            $image->save(storage_path('app/' . $this->user_image_folder . '/' . $filename));
            return $filename;
        }
    }

    /**
     * @param string $image
     * @param string $size
     * @return bool|string
     */
    protected function imagePath(string $image, $type = 'small'){
        if ($type === 'full'){// full size
            $path = storage_path( 'app/' . $this->lg_folder . '/' . $this->lg_prefix . $image);
            return file_exists($path) ? $path : false;
        }
        if ($type === 'profile_background'){
            $path = storage_path( 'app/' . $this->sm_folder . '/' . $this->bg_image_prefix . $image);
            return file_exists($path) ? $path : false;
        }
        if ($type === 'profile_image'){
            $path = storage_path( 'app/' . $this->sm_folder . '/' . $this->profile_image_prefix . $image);
            return file_exists($path) ? $path : false;
        }
        // small size1
        $path = storage_path( 'app/' . $this->sm_folder . '/' . $this->sm_prefix . $image);
        return file_exists($path) ? $path : false;
    }

    /**
     * @param string $image
     * @param string $size
     * @return string url of the given image
     * @return bool false if there is no existing file
     */
    protected function imageUrl(string $image, $size = 'small'){
        if ($size === 'full'){
            return $this->imagePath($image, 'full') ?
                Storage::url( $this->lg_folder . '/' . $this->lg_prefix . $image) : false;
        }
        // small size
        return $this->imagePath($image) ?
            Storage::url($this->sm_folder . '/' . $this->sm_prefix . $image) : false ;
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

