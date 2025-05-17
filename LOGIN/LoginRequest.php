<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Models\Pengguna;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Update validasi
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    // Update pesan error
    public function messages(): array
    {
        return [
            'email.required' => 'Harap lengkapi email atau nomor WhatsApp',
            'password.required' => 'Harap lengkapi kata sandi anda',
            'password.min' => 'Kata sandi minimal :min karakter',
        ];
    }
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Cari pengguna berdasarkan email/no_whatsapp
        $pengguna = Pengguna::where('phone_number', $this->email)->first();
        $user = User::where('email', $this->email)->first();

        // Prioritaskan pengecekan pengguna terlebih dahulu
        if ($pengguna) {
            // Pengecekan role sebelum pengecekan password
            if ($pengguna->id_role == 1) {
                throw ValidationException::withMessages([
                    'email' => 'Anda adalah user desa, Silahkan login ke aplikasi.',
                ]);
            }

            $this->checkPenggunaPassword($pengguna);
            return;
        }

        // Jika bukan pengguna, cek user biasa
        if ($user) {
            $this->checkUserPassword($user);
            return;
        }

        // Jika tidak ditemukan sama sekali
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    // Cek password untuk User
    protected function checkUserPassword($user): void
    {
        if (!Hash::check($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => trans('auth.failed'),
            ]);
        }

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $this->session()->regenerate();
    }

    // Cek password untuk Pengguna
    protected function checkPenggunaPassword($pengguna): void
    {
        // Pastikan hanya role 2 yang bisa login
        if ($pengguna->id_role != 2) {
            throw ValidationException::withMessages([
                'email' => 'Akun anda tidak memiliki akses ke sistem ini.',
            ]);
        }

        // Pengecekan password hanya dilakukan jika role adalah 2
        if (!Hash::check($this->password, $pengguna->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => 'Password yang anda masukkan salah.',
            ]);
        }

        // Jika login berhasil
        Auth::guard('pengguna')->login($pengguna, $this->boolean('remember'));
        $this->session()->regenerate();

        Log::debug("Pengguna ID: " . $pengguna->id);
        Log::debug("Role: " . $pengguna->id_role);
        Log::debug("Input Password: " . $this->password);
        Log::debug("Database Password: " . $pengguna->password);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
