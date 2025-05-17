<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'password' => ['required', 'min:8', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'min:8'],
            'token' => ['required'],
            'email' => ['required', 'email'],
        ], [
            'password.required' => 'Harap lengkapi kata sandi anda',
            'password.min' => 'Kata sandi minimal :min karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai',
            'password_confirmation.min' => 'Kata sandi minimal :min karakter',
            'password_confirmation.required' => 'Harap lengkapi kata sandi anda',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('reset', 'Password berhasil diubah')
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
