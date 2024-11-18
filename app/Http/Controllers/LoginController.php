<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


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
                'status' => false,
                'code' => '404',
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Set the token expiration to 10 minutes
        $customClaims = ['exp' => now()->addMinutes(10)->timestamp];
        $add_token = JWTAuth::claims($customClaims)->fromUser($user);

        $userData = $user->toArray();
        unset($userData['add_token']);

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Login Successful',
            'user' => $userData,
            'token' => $add_token,
        ], 200);
    }


    /** Function used for refresh page then token change by ns */
    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            $user = JWTAuth::setToken($newToken)->toUser();

            $user->save();
            $userArray = $user->toArray();
            unset($userArray['add_token']); 

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Token refreshed successfully',
                'token' => $newToken, 
                'user' => $userArray,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'Token is invalid',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => false,
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
                'status' => true,
                'code' => '200',
                'message' => 'User logged out successfully',
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'Token is invalid',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Could not log out user',
            ], 500);
        }
    }

    /** Function used for the send otp in mail by ns */

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => '400',
                'message' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'User not found.'
            ], 404);
        }

        $otp = rand(100000, 999999);

        \App\Models\Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new \App\Mail\OtpMail($otp, $user));

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'OTP sent to your email.'
        ], 200);
    }

    /** funtion used for the verify otp by ns */

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => '400',
                'message' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'User not found.'
            ], 404);
        }

        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'code' => '400',
                'message' => 'Invalid or expired OTP.'
            ], 400);
        }

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'OTP Verified Successfully.'
        ], 200);
    }

    /** Function used for the forget password by ns */

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|integer',
            'password' => ['required', 'regex:/^(?=.*\d)(?=.*[A-Z])(?=.*\W).{8,16}$/', 'confirmed'],
            'password_confirmation' => ['required', 'regex:/^(?=.*\d)(?=.*[A-Z])(?=.*\W).{8,16}$/'],
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('password')) {
                return response()->json([
                    'status' => false,
                    'code' => '400',
                    'message' => 'Password confirmation does not match.'
                ], 400);
            }
            return response()->json([
                'status' => false,
                'code' => '400',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => '404',
                'message' => 'User not found.'
            ], 404);
        }

        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'code' => '400',
                'message' => 'Invalid or expired OTP.'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $otpRecord->forceDelete();

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'Password Reset Successfully.'
        ], 200);
    }
}
