<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\Setting;
use App\PasswordReset;
use Illuminate\Support\Facades\Mail;
class PasswordResetController extends Controller
{

    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user){
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.' ], 404);
        }
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );
        if ($user && $passwordReset){
            $url = url('/find/'. $passwordReset->token);
            Mail::to($user)->send(new \App\Mail\resetPassword($url));
            return response()->json([
                'message' => 'We have e-mailed your password reset link!'
            ], 201);
        }
    }


    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();

        if (!$passwordReset){
//            This password reset token is invalid.
            abort(404);
        }


        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
//            'This password reset token is invalid.'
            abort(404);
        }
        return view('auth.reset_password_form', compact('passwordReset'));
    }


    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    protected function reset($request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:8',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        $user = User::where('email', $passwordReset->email)->first();

        if (!$user){
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        return $user->loadMissing(
            'seenComments', 'designs', 'following',
            'followers', 'likedDesigns', 'comments');
    }

    public function resetApi(Request $request)
    {
        $user = $this->reset($request);

        return response()->json($user->loadMissing(
            'seenComments', 'designs', 'following',
            'followers', 'likedDesigns', 'comments'), 201);
    }

    public function resetWeb(Request $request)
    {
        $user = $this->reset($request);

        session()->flash('changed_pass_msg' , "رمز عبور شما با موفقیت تغییر کرد");
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
}
