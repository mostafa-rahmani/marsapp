<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 12/23/2018
 * Time: 11:40 AM
 */
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

/**
 * delete_image function
 *@returns void
 *@params string, boolean
 * */
if (! function_exists('delete_image')) {
    function delete_image($imageName , $profile = false, $adminAbout = false)
    {
        if ($profile && file_exists(storage_path('app/public/profile_image_' . $imageName))) {
            $result = Storage::disk('public')->delete($imageName);
            return $result ? true : false;
        }
        if ($adminAbout && file_exists(storage_path('app/public/' . $imageName)) ){
            $result = Storage::disk('public')->delete($imageName);
            return $result ? true : false;
        }
        if (file_exists(storage_path('app/full_size/lg_' . $imageName))) {
            Storage::disk('public')->delete('/lg_' . $imageName);
            if (file_exists(storage_path('app/public/sm_' . $imageName))) {
                Storage::disk('public')->delete('/sm_' . $imageName);
            }
            return true;
        }
        return false;
    }
}

/**
 * store_design_image function
 *@returns array
 *@params string, boolean
 * */
if (! function_exists('store_design_image')) {
    function store_design_image($image){
        $extension = $image->getClientOriginalExtension();
        $filename = date('Y-m-d_h-m-s') . '_' . str_random('4') . '.' . $extension;
        $fullSizeName = 'lg_' . $filename;
        $smallSizeName = 'sm_' . $filename;
        $thumbnail_width = 900;

        $image = Image::make($image->getRealPath());
        $image->save(storage_path( 'app/full_size/' . $fullSizeName));
        $data['original_width']  =  $image->getWidth();
        $data['original_height'] = $image->getHeight();
        if ($data['original_width'] > $thumbnail_width){
            $image->widen($thumbnail_width,  function ($constraint) {
                $constraint->upsize();
            });
            $image->save(storage_path('app/public/' . $smallSizeName));
        }else{
            $image->save(storage_path('app/public/' . $smallSizeName));
        }
        $data['image'] = $filename;
        return $data;
    }
}

/**
 * store_user_image function
 *@returns array
 *@params string, boolean
 * */
if (! function_exists('store_user_image')) {
    /**
     * @param $image
     * @param $type
     * @return string
     */
    function store_user_image($image, $type){
        $extension = $image->getClientOriginalExtension();
        $user = auth()->user();
        if ($type == 'profile_background'){
            $filename  = 'profile_bg_' . date('Y-m-d_h-m') .  "_{$user->id}_" . '.' . $extension;
            $image = Image::make($image->getRealPath());
            $image->widen(900,  function ($constraint) {
                $constraint->upsize();
            });
            $image->save(storage_path('app/public/' . $filename));
            return $filename;
        }
        if ($type == 'profile_image'){
            $filename  = 'profile_image_' . date('Y-m-d_h-m') .  "_{$user->id}_" . '.' . $extension;
            $image = Image::make($image->getRealPath());
            $image->widen(100,   function ($constraint) {
                $constraint->upsize();
            });// needs to be square
            $image->save(storage_path('app/public/'. $filename));
            return $filename;
        }
    }
}

/**
 * @param string $image
 * @param string $type
 * @return bool|string
 */
if (! function_exists('image_path')) {
    function image_path(string $image, $type = 'small')
    {
        if ($type === 'full') {// full size
            $path = storage_path('app/full_size/' . 'lg_' . $image);
            return file_exists($path) ? $path : false;
        }
        if ($type === 'profile_background') {
            $path = storage_path('app/public/' . 'profile_bg_' . $image);
            return file_exists($path) ? $path : false;
        }
        if ($type === 'profile_image') {
            $path = storage_path('app/public/' . 'profile_image_' . $image);
            return file_exists($path) ? $path : false;
        }
        // small size
        $path = storage_path('app/public/' . 'sm_' . $image);
        return file_exists($path) ? $path : false;
    }
}

/**
 * @param string $imageName
 * @param string $type
 * @return string url of the given image
 */
function image_url(string $imageName, $type = 'sm'){
    if ($type === 'sm'){// sm -> small size
        return image_path($imageName, 'small') ?
            Storage::url( 'public/sm_'  . $imageName ) : false ;
    }
    if ($type === 'pi'){// pi -> profile image
        return image_path($imageName, 'profile_image') ?
            Storage::url('public/profile_image_' . $imageName) : false ;
    }
    if ($type === 'pb'){// pb -> profile background
        return image_path($imageName, 'profile_image') ?
            Storage::url('public/profile_image_' . $imageName) : false ;
    }
}

