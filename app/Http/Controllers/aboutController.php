<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class aboutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('about');
    }

    public function about()
    {
        $settings = Setting::first();
        $footer_links = DB::table('footer_link')->get();
        return view('about', compact('settings', 'footer_links'));
    }

    public function aboutAdmin()
    {
        $settings = Setting::first();
        $settings->about_second_img = Storage::url('public/' . $settings->about_second_img);
        $settings->about_first_img = Storage::url('public/' . $settings->about_first_img);
        return view('admin.about', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->only('about_first_text', 'about_second_text', 'about_first_img', 'about_second_img');
        $settings = Setting::first();
        if ($image = $request->file('about_first_img')){
            ini_set('memory_limit','256M');
            $filename = 'about_first_img_';
            $this->imageDelete($settings->about_first_img);
            $data['about_first_img'] = $this->saveImage($image, $filename);
        }
        if ($image = $request->file('about_second_img')){
            $filename = 'about_second_img_';
            $this->imageDelete($settings->about_second_img);
            $data['about_second_img'] = $this->saveImage($image, $filename);
        }
        if (Setting::first()->update($data)){
            session()->flash('message', 'تنظیمات با موفقیت به روزرسانی شد.');
            return redirect()->back();
        }
        session()->flash('message', 'بروزرسانی تنظیمات موفق نبود لطفا دوباره تلاش کنید.');
        return redirect()->back();
    }

    private function saveImage($image, $prefix){
        $extension = $image->getClientOriginalExtension();
        $filename = $prefix . '.' . $extension;
        $image = Image::make($image->getRealPath());
        $image->widen(300 ,  function ($constraint) {
            $constraint->upsize();
        });
        $image->save(storage_path( 'app/public/' . $filename));
        return $filename;
    }
    private function imageDelete($filename){
        if (file_exists(storage_path('app/public/' . $filename))){
            return Storage::delete('public/' . $filename);
        }
        return true;
    }
}
