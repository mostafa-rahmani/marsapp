<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Gate;


class AuthController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth')->except('adminLogin', 'registerForm');
    }
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePass(Request $request)
    {
        if ($request->method() === 'POST'){
            $this->validate($request, [
                'password' => 'required',
                'new_password' => 'required|min:8|confirmed'
            ]);
            $user = User::where("email", auth()->user()->email)->first();
            if (Auth::validate(
                ["email" => $user->email, "password" => $request->input('password')]
            )){
                $user->password = bcrypt($request->input('new_password'));
               if ( $user->save()){

                   session()->flash('message', 'رمز عبور شما تغییر کرد');
                   return redirect('/admin');
               }
            }
            session()->flash('message', 'رمز عبور شما اشتباه است. لطفا دوباره تلاش کنید.');
            return redirect('/admin');
        }
        return view('admin.auth.reset_pass');
    }

    public function registerForm()
    {
        if (Gate::allows('admin-register')){// if admin registeration is allowed

            return view('admin.auth.register');
        }
        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username'   => 'required|unique:users',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed'
        ]);
        $user = new User($request->only('password', 'username', 'email'));
        if ($user->save()){
            // attach user role to new registered user
            $user->roles()->attach(2);
            session()->flash('message', 'حساب کاربری ایجاد شد');
            return redirect('/auth/login');
        }
        session()->flash('message', 'ثبت نام با موفق نبود لطفا دوباره تلاش کنید.');
        return redirect('/auth/register');
    }
}
