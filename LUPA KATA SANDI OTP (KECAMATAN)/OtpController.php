<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class OtpController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot_password_otp');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|regex:/^08[0-9]{9,12}$/'
        ]);

        $user = Pengguna::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return back()->withErrors(['phone_number' => 'Nomor tidak ditemukan.']);
        }

        $otp = rand(1000, 9999);
        $user->kode_otp = $otp;
        $user->updated_at = now();
        $user->save();

        // Ubah 08xxx menjadi 62xxx (format internasional untuk Fonnte)
        $target = preg_replace('/^0/', '62', $request->phone_number);
        $message = "Kode OTP untuk reset sandi Anda adalah $otp. Berlaku 10 menit.";

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_API_KEY'),
                'Content-Type' => 'application/json'
            ])->post('https://api.fonnte.com/send', [
                'target'  => $target,
                'message' => $message
            ]);

            $result = $response->json();

            if (!$response->successful() || empty($result['status']) || $result['status'] !== true) {
                $reason = $result['reason'] ?? 'Gagal mengirim OTP.';
                return back()->withErrors(['phone_number' => "Gagal kirim OTP: $reason"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['phone_number' => 'Gagal mengirim OTP. Silakan coba lagi.']);
        }

        return redirect()->route('otp.verify.form')->with('phone_number', $request->phone_number);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'otp' => 'required|digits:4',
        ]);

        $user = Pengguna::where('phone_number', $request->phone_number)
            ->where('kode_otp', $request->otp)
            ->where('updated_at', '>', now()->subMinutes(10))
            ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'OTP salah atau sudah kadaluarsa.']);
        }

        $user->kode_otp = null;
        $user->save();

        return view('auth.reset_password_otp', ['phone_number' => $request->phone_number]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Pengguna::where('phone_number', $request->phone_number)->first();
        if (!$user) {
            return back()->withErrors(['phone_number' => 'Nomor tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->kode_otp = null;
        $user->save();

        return redirect('/login')->with('success', 'Password berhasil diubah. Silakan login.');
    }
}
