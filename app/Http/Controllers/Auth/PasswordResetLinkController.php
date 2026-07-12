<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle checking email and resetting password.
     */
    public function store(Request $request): RedirectResponse|View
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem.']);
        }

        if ($request->has('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);

            $user->forceFill([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'remember_token' => \Illuminate\Support\Str::random(60),
            ])->save();

            return redirect()->route('login')->with('status', 'Password Anda berhasil diperbarui. Silakan login.');
        }

        return view('auth.forgot-password', [
            'email' => $request->email,
            'email_verified' => true
        ]);
    }
}
