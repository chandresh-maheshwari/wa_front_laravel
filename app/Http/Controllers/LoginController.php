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
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $remember = $request->has('remember') && $request->remember;

        $user->remember_me = $remember;
        $user->save();
        
        // $expireMinutes = $remember; 
        $expireToken = ['exp' => now()->addMinutes(60)->timestamp];
        $add_token = JWTAuth::claims($expireToken)->fromUser($user);
        $user->add_token = $add_token;
        $user->save();
        $userData = $user->toArray();
        unset($userData['add_token']);

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'User Login Successfully',
            'user' => $userData,
            'token' => $add_token,
        ], 200);
    }
    public function refresh(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $user = User::findOrFail($userId);

            $newToken = JWTAuth::fromUser($user);

            $user->add_token = $newToken;
            $user->save();

            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'Token Refreshed Successfully',
                'data' => [
                    'add_token' => $newToken,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Could Not Refresh Token',
                'data' => [
                    'headers' => [],
                    'original' => [
                        'status' => false,
                        'message' => 'Could Not Refresh Token',
                        'data' => null,
                    ],
                    'exception' => $e->getMessage(),
                ],
            ], 500);
        }
    }



    /** Function used for user logout by ns */

    public function logoutpage(Request $request)
    {
        try {
            if (!JWTAuth::parseToken()->check()) {
                return response()->json([
                    'status' => false,
                    'code' => '400',
                    'message' => 'User Already Logged Out',
                ], 400);
            }

            JWTAuth::invalidate(JWTAuth::parseToken());
            return response()->json([
                'status' => true,
                'code' => '200',
                'message' => 'User Log Out Successfully',
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'code' => '401',
                'message' => 'Token Is Invalid',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => false,
                'code' => '500',
                'message' => 'Could Not Logout User',
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
                'message' => 'User Not Found.'
            ], 404);
        }

        $otp = rand(100000, 999999);

        \App\Models\Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(60),
        ]);

        Mail::to($user->email)->send(new \App\Mail\OtpMail($otp, $user));

        return response()->json([
            'status' => true,
            'code' => '200',
            'message' => 'OTP Sent To Your Email.'
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
                'message' => 'User Not Found.'
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
                'message' => 'Invalid Or Expired OTP.'
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
                    'message' => 'Password Confirmation Does Not Match.'
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
                'message' => 'User Not Found.'
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
                'message' => 'Invalid Or Expired OTP.'
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
