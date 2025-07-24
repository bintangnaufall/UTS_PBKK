<?php

// app/Http/Controllers/Auth/OTPController.php
namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class OTPController extends Controller
{
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        // Simpan OTP di cache selama 5 menit
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        // Kirim OTP lewat email
        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Your OTP Code');
        });

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'otp' => 'required'
        ]);
        
        $cachedOtp = Cache::get('otp_' . $request->email);
        if ($cachedOtp && $cachedOtp == $request->otp) {
            User::create([
                'name' => $request->name,
                'membership_date' => Carbon::now(),
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            return response()->json([
                'message' => 'OTP verified successfully.',
                'success' => true
            ]);
        }
        return response()->json(['message' => 'Invalid or expired OTP.'], 400);
    }
}

