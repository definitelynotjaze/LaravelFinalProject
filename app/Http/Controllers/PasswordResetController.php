<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    // Step 1: Show "enter email" form
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Step 2: Send code to email
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Always show success to prevent email enumeration
        if (!$user) {
            return back()->with('success', 'If that email is registered, a code has been sent.');
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store code in session (expires with session, ~2 hours by default)
        session([
            'reset_email'   => $user->email,
            'reset_code'    => Hash::make($code),
            'reset_code_at' => now()->timestamp,
        ]);

        // Send email
        Mail::raw(
            "Hi {$user->first_name},\n\nYour BrewCraft password reset code is:\n\n{$code}\n\nThis code expires in 15 minutes. If you did not request this, please ignore this email.\n\n— BrewCraft",
            function ($message) use ($user, $code) {
                $message->to($user->email, $user->name)
                        ->subject('BrewCraft Password Reset Code: ' . $code);
            }
        );

        return redirect()->route('password.code-form')
            ->with('success', 'A 6-digit code was sent to ' . $user->email);
    }

    // Step 3: Show "enter code" form
    public function showCodeForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-code');
    }

    // Step 4: Verify code
    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $email   = session('reset_email');
        $hash    = session('reset_code');
        $sentAt  = session('reset_code_at');

        if (!$email || !$hash) {
            return redirect()->route('password.request')->with('error', 'Session expired. Please try again.');
        }

        // 15-minute expiry
        if (now()->timestamp - $sentAt > 900) {
            return back()->with('error', 'Code has expired. Please request a new one.');
        }

        if (!Hash::check($request->code, $hash)) {
            return back()->with('error', 'Incorrect code. Please try again.');
        }

        // Code is valid — issue a reset token
        $token = Str::random(64);
        session(['reset_token' => $token, 'reset_verified' => true]);

        return redirect()->route('password.reset-form');
    }

    // Step 5: Show "set new password" form
    public function showResetForm()
    {
        if (!session('reset_verified')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }

    // Step 6: Save new password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'token'    => 'required',
        ]);

        if (!session('reset_verified') || session('reset_token') !== $request->token) {
            return redirect()->route('password.request')->with('error', 'Invalid session. Please start over.');
        }

        $email = session('reset_email');
        $user  = User::where('email', $email)->firstOrFail();

        $user->update(['password' => Hash::make($request->password)]);

        // Clear all reset session data
        session()->forget(['reset_email','reset_code','reset_code_at','reset_token','reset_verified']);

        return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in.');
    }
}
