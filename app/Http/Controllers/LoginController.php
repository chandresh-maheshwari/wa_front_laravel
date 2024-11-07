<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /** Function used for user login by ns */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'Error',
                'code' => '404',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);
        $user->token = $token;
        $user->makeHidden(['token']);
        $user->save();

        return response()->json([
            'status' => 'Success',
            'code' => '200',
            'message' => 'Login Successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /** Function used for refresh page then token change by ns */
    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            $user = JWTAuth::setToken($newToken)->toUser();
            $user->token = $newToken;
            $user->save();

            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'Token refreshed successfully',
                'user' => $user,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'Token is invalid',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'code' => '500',
                'message' => 'Could not refresh token',
            ], 500);
        }
    }

    /** Function used for user logout by ns */
    public function logoutpage(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
            return response()->json([
                'status' => 'Success',
                'code' => '200',
                'message' => 'User logged out successfully',
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => 'Error',
                'code' => '401',
                'message' => 'Token is invalid',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'Error',
                'code' => '500',
                'message' => 'Could not log out user',
            ], 500);
        }
    }
}
