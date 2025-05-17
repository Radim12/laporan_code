<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        // Ambil user berdasarkan guard yang sedang login
        $user = Auth::guard('pengguna')->check()
            ? Auth::guard('pengguna')->user()
            : Auth::user();

        // Tentukan tipe user untuk view
        $userType = Auth::guard('pengguna')->check() ? 'pengguna' : 'user';

        return view('backend.profile', compact('user', 'userType'));
    }

    public function update(Request $request, string $id)
    {
        // Tentukan guard dan model yang digunakan
        if (Auth::guard('pengguna')->check()) {
            $user = Pengguna::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'nomer_telepon' => [
                    'required',
                    'string',
                    'regex:/^62\d{10,13}$/'
                ],
            ], [
                'nomer_telepon.regex' => 'Nomor telepon harus diawali 62 dan terdiri dari 12-15 digit angka',
                'name.max' => 'Nama tidak boleh lebih dari :max karakter',
            ]);

            $user->full_name = $request->name;
            $user->phone_number = $request->nomer_telepon;
        } else {
            $user = User::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'nomer_telepon' => [
                    'required',
                    'string',
                    'regex:/^62\d{10,13}$/'
                ],
                'alamat' => 'required|string|max:255',
            ], [
                'nomer_telepon.regex' => 'Nomor telepon harus diawali 62 dan terdiri dari 12-15 digit angka',
                'name.max' => 'Nama tidak boleh lebih dari :max karakter',
                'alamat.max' => 'Alamat tidak boleh lebih dari :max karakter',
            ]);

            $user->name = $request->name;
            $user->nomer_telepon = $request->nomer_telepon;
            $user->alamat = $request->alamat;
        }

        $user->save();

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui');
    }
}