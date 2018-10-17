<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('login', 'register');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
           'username'   => 'required|unique:users',
           'email'      => 'required|email|unique:users',
           'password'   => 'required|min:8|confirmed'
        ]);
        $user = new User([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);
        if ($user->save()){
            return response()->json(['msg' => 'User created', 'user' => $user], 200);
        }
        return response()->json(['msg' => 'some thing went wrong try again'], 404);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * checkes if password is correct or not
     * @param password, email
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function checkPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::guard('web')->attempt($credentials)){
            $message = "password is not valid";
            $is_valid = false;
        }else{
            $message = "password is valid";
            $is_valid = true;
        }
        $response = [
          "message " => $message,
          "is_valid" =>   $is_valid
        ];
        return response()->json($response, 200);
    }


    /**
     * changes password
     * @param password, email
     * */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);
        $data = [
            "email" => $request->email,
            "password" => $request->password
        ];
        $user = User::where('email', '=', $data["email"])->first();
        $user->password = bcrypt($data["password"]);
        $user->save();
        // a email sed to user
        return response()->json($user);
    }

}
