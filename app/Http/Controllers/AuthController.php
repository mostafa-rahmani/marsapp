<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
           'username'   => 'required',
           'email'      => 'required|email',
           'password'   => 'required|min:8'
        ]);
        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);
        if ($user->save()){
            $user->signin = [
                'href' => 'api/v2/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];
            return response()->json(['msg' => 'User created', 'user' => $user], 200);
        }
        return response()->json(['msg' => 'an error occured'], 404);
    }

    public function login(Request $request)
    {
        $this->validate($request, array(
            'email' => 'required|email',
            'password' => 'required'
        ));

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }



}
