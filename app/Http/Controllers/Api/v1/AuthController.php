<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    // ... (keep sendOtp as is) ...
    public function sendOtp(Request $request)
    {
        $mobile = $this->normalizeMobile($request->input('mobile'));

        $validator = Validator::make(
            ['mobile' => $mobile],
            ['mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/']],
            ['mobile.regex' => 'فرمت شماره موبایل صحیح نیست. (مثال: 09123456789)']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // For development/demo purposes
        $otp = '1234';

        Cache::put('otp_' . $mobile, $otp, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'message' => 'کد تایید ارسال شد.',
            // In production, remove 'code' from response
            'development_code' => $otp, 
            'mobile' => $mobile
        ]);
    }

    /**
     * Verify OTP, find/create user, and return Token with Next Action.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'code' => 'required|numeric|digits:4',
        ]);

        $mobile = $this->normalizeMobile($request->input('mobile'));
        $cacheKey = 'otp_' . $mobile;
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'کد منقضی شده است.'], 419);
        }

        if ($request->input('code') != $cachedOtp) {
            return response()->json(['success' => false, 'message' => 'کد وارد شده صحیح نمی‌باشد.'], 401);
        }

        Cache::forget($cacheKey);

        $user = null;
        $guard = 'web';
        $abilities = ['role:user'];

        // Check if admin
        $admin = Admin::where('mobile', $mobile)->first();

        if ($admin) {
            $user = $admin;
            $guard = 'admin';
            $abilities = ['role:admin'];
        } else {
            // Find or Create User
            // We DO NOT set a default name here anymore.
            // This allows us to detect if the profile is incomplete.
            $user = User::firstOrCreate(
                ['mobile' => $mobile]
            );
            $guard = 'web';
        }

        $user->tokens()->delete();
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        // Determine Next Action for the Frontend
        $nextAction = 'dashboard';
        if ($guard === 'web') {
            if (empty($user->name)) {
                $nextAction = 'register_name'; // Step 2: Show Name Form
            } elseif (empty($user->email)) {
                $nextAction = 'register_email'; // Step 3: Show Email Form
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ورود با موفقیت انجام شد.',
            'token' => $token,
            'user' => $user,
            'guard' => $guard,
            'next_action' => $nextAction, // frontend checks this string
        ]);
    }

    // ... (keep normalizeMobile and logout as is) ...
    private function normalizeMobile(string $mobile): string
    {
        $mobile = preg_replace('/[^\d]/', '', $mobile);
        if (str_starts_with($mobile, '98')) {
            $mobile = '0' . substr($mobile, 2);
        }
        if (strlen($mobile) == 10 && str_starts_with($mobile, '9')) {
            $mobile = '0' . $mobile;
        }
        return $mobile;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'خروج با موفقیت انجام شد.']);
    }
}