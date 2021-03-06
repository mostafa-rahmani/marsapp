<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\Design as DesignResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\SignUp;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('login', 'register');
    }

    public function register(SignUp $request)
    {

        $user = new User([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'profile_image' => null,
            'profile_background'    => null,
            'blocked'      => null,
            'instagram'     => null,
            'bio'   => null
        ]);
        if ($user->save()){
            // attach user role to new registered user
            $user->roles()->attach(2);
            $response = [
                "status" => "ok",
                "code"  => "200",
                "message"   => "user created successfully",
                "returned"  => "the created user",
                "data"    => [
                    "user"    => new UserResource($user),
                    "users" => null,
                    "design" => null,
                    "designs" => null,
                    "comment" => null,
                    "comments" => null,
                ]
            ];
            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        }
        return response()->json(['msg' => 'some thing went wrong try again'], 500);
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
                'status'  =>  'error',
                'code'  =>  401,
                'message'  =>  'رمز عبور یا ایمیل صحیح نسیت',
                'returned'  =>  null,
                'data'  => [
                  'user'  =>  null,
                  'users'  =>  null,
                  'design'  =>  null,
                  'designs'  =>  null,
                  'comment'  =>  null,
                  'comments'  =>  null
                ]
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'status' => 'ok',
            'code'  =>  '200',
            'message'   => 'User logged in successfully',
            'returned'  => 'Authenticated user object, access_token, token_type, expires_at',
            'data' => [
                'user'  =>  new UserResource($user),
                'users' => null,
                'design'    => null,
                'designs'   => null,
                'comment'   => null,
                'comments'  => null,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]
        ]);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json(new UserResource($request->user) , 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
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
            'status'  => "ok",
            'code'      =>  '200',
            'message'   => $message,
            'returned'  =>  $is_valid,
            'data'      =>  [
                "user"      => null,
                "users"     => null,

                "design" => null,
                "designs"    => null,

                "comment"    => null,
                "comments"   => null
            ]
        ];
        return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
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
        return response()->json(new UserResource($user) , 200, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

}
