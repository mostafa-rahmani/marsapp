<?php

namespace App\Http\Controllers;

use App\Design;
use App\Setting;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
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

        $this->middleware('auth')->except('home', 'about');
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

    public function toggleManager(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|Exists:users,username|String'
        ]);
        $user = User::where('username', $request->input('username'))->first();
        if ($user->isManager()){
            session()->flash('message' , 'در حال حاضر مدیر است.' . $user->username . 'کاربر ');
            return redirect()->back();
        }
        $user->roles()->attach(1);
        session()->flash('message' , 'به مدیر تغییر کرد' . $user->username . 'کاربر ');
        return redirect()->back();
    }

    public function removeManager(User $user)
    {
        if ($user->roles()->detach(1)){
            session()->flash('message' , 'از مدیریت برکنار شد' . $user->username . 'کاربر ');
            return redirect()->back();
        }
        session()->flash('message' , 'لطفا دوباره تلاش کنید');
        return redirect()->back();

    }

    public function admin()
    {
        $users =  User::orderBy('created_at', 'desc')->take(5)->get();
        $users_count = User::all()->count();
        $designs_count = Design::all()->count();
        $current_user = auth()->user();
        return view('admin.dashboard', compact('users', 'designs_count', 'users_count', 'current_user'));
    }

    public function home()
    {
        $data = Setting::first();
        if (!isset($data)){
            $data = [
                'landing_title' => ' اپلیکیشن پرتقال برای تمام طراحان است',
                'landing_description' => 'طراح هستید یا نقاش و شاید هنرمند، اپ پرتقال رو نصب کنید و ایده ها و طرح هاتون رو با هم به اشتراک بزارید و بازخورد دوستاتون رو هم داشته باشید.',
                'app_download_url' => 'cafebazaar.ir',
                'admin_register_on' => 1
            ];
        }
        return view('welcome' , compact('data'));
    }

    public function allUsers()
    {
    	$users = DB::table('users')->paginate(10);
		return view('admin.users', compact('users'));
    }

    public function showUser(User $user)
    {
    	return view('admin.user', compact('user'));
    }

    public function adminSettings()
    {
        $settings = Setting::firstOrFail();
        $users = User::all();
        $footer_links = DB::table('footer_link')->get();
    	return view('admin.settings', compact('settings', 'users', 'footer_links'));
    }

    public function updateSettings(Request $request)
    {
        $this->validate($request, [
            'landing_description' => 'String|min:125',
            'landing_title' => 'String|min:20',
            'admin_register_on' => 'Boolean',
            'app_download_url' => 'String'
        ]);

        $data = $request->only('landing_description', 'app_download_url', 'landing_title', 'admin_register_on');
        if (Setting::first()->update($data)){
            session()->flash('message', 'تنظیمات با موفقیت به روزرسانی شد.');
            return redirect()->back();
        }

        session()->flash('message', 'بروزرسانی تنظیمات موفق نبود لطفا دوباره تلاش کنید.');
        return redirect()->back();
    }

    public function blockUser(User $user)
    {
        $user->blocked = !$user->blocked;
        $user->save();
        return redirect()->back();
    }

    public function blockDesign(Design $design)
    {
        $design->blocked = !$design->blocked;
        $design->save();
        return redirect()->back();
    }

    public function footerSettings(Request $request)
    {
        $data = $request->only('web_developer_img', 'web_developer_url', 'android_developer_img', 'android_developer_url');
        if ($image = $request->file('web_developer_img')){
            ini_set('memory_limit','256M');
            $filename = 'web_developer_img_' . '.' . $image->getClientOriginalExtension();
            $img = Image::make($image->getRealPath());
            $img->resize(400, 400)->save(storage_path('app/public/' . $filename));
            $data['web_developer_img'] = $filename;
        }
        if ($image = $request->file('android_developer_img')){
            $filename = 'android_developer_img_' . '.' . $image->getClientOriginalExtension();
            $img = Image::make($image->getRealPath());
            $img->resize(400, 400)->save(storage_path('app/public/' . $filename));
            $data['android_developer_img'] = $filename;
        }
        if (Setting::first()->update($data)){
            session()->flash('message', 'تنظیمات با موفقیت به روزرسانی شد.');
            return redirect()->back();
        }
        session()->flash('message', 'بروزرسانی تنظیمات موفق نبود لطفا دوباره تلاش کنید.');
        return redirect()->back();

    }

    public function apkUpload(Request $request)
    {
        ini_set('memory_limit','10240M');
        try {
            $this->validate($request, [
                'apk'   =>  'Required|File'
            ]);
        } catch (ValidationException $e) {
            session()->flash('message', 'فایل ارسالی باید یک فایل apk باشد. و نباید خالی فرستاده شود.');
            return redirect()->back();
        }

        $file = $request->file('apk');
        $filname = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        if ($extension !== 'apk'){
            session()->flash('message', 'فایل ارسالی باید یک فایل apk باشد.');
            return redirect()->back();
        }
        if(file_exists(storage_path('app/mars.apk'))){
            Storage::delete('mars.apk');
        }
        $file->storeAs('/','mars.' . $extension , 'app' );
        session()->flash('message', 'قایل با موفقیت ذخیره شد.');
        return redirect()->back();
    }

    public function download()
    {
        if(file_exists($path = storage_path('app/mars.apk'))){
            return Response::download($path);
        }
        session()->flash('message', 'فایل در حال حاضر موجود نیست. لطفا مدتی منتظر بمانید.');
        return redirect()->back();
    }

    public function footerLinks(Request $request)
    {

        if ($request->method() == 'POST'){
            $data = $request->only('footer_link', 'footer_url');
            DB::table('footer_link')->insert($data);
            session()->flash('message', 'لینک ها با موفقیت ذخیره شدند.');
            return redirect()->back();
        }else{
            $data = $request->only('footer_links');
            foreach ($data as $item){
                foreach ($item as $value){
                    if ($row = DB::table('footer_link')->where('id', $value['id'])){
                        $row->update($value);
                    }
                }
            }
            session()->flash('message', 'لینک ها با موفقیت ذخیره شدند.');
            return redirect()->back();
        }
    }

    public function deleteLink(Request $request)
{
        $id = $request->id;
        if ($row = DB::table('footer_link')->where('id', $id)){
            $row->delete();
        }

        session()->flash('message', 'لینک با موفقیت حذف شد');
        return redirect()->back();
    }

}
