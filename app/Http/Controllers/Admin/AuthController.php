<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller
{
    public function adminLogout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function adminLogin(Request $request)
    {

        if ($request->method() === 'POST'){

            if(!User::where('email' , $request->email)->first()->isManager()){
                session()->flash('message', 'شما اجازه ورود ندارید. لطفا از اپلیکیشن برای ورود استفاده کنید');
                return redirect('/');
            }
            if (Auth::attempt($request->only('email', 'password'), $request->input('remember_me'))) {
                return redirect('/admin');
            }
            return redirect('/');
        }
        return view('admin.auth.login');
    }

    public function changePass(Request $request)
    {
        if ($request->method() === 'POST'){
            $this->validate($request, [
                'password' => 'required',
                'new_password' => 'required|min:8|confirmed'
            ]);
            $data = [
                "password" => $request->password
            ];
            $user = auth()->user();
            $user->password = bcrypt($data["password"]);
            $user->save();
            // a email sed to user
            session()->flash('message', 'رمز عبور شما تغییر کرد');
            return redirect('/');
        }
        return view('admin.auth.reset_pass');
    }

    public function register()
    {

    }
}
