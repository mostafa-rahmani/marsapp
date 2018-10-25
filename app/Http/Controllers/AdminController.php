<?php

namespace App\Http\Controllers;

use App\Design;
use App\Setting;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth')->except('home');
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
                'landing_title' => 'اپلیکیشن پرتقال برای تمام طراحان',
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
    	$designs = $user->designs()->get();
    	return view('admin.user', compact('user', 'designs'));
    }

    public function adminSettings()
    {
        $settings = Setting::firstOrFail();
        $users = User::all();
    	return view('admin.settings', compact('settings', 'users'));
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

}
