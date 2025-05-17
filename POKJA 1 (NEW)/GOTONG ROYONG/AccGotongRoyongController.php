<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\GotongRoyong;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;

class AccGotongRoyongController extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan data dengan status Disetujui1 dan Disetujui2
            $got1 = GotongRoyong::whereIn('status', ['Disetujui1'])->count();
            $got2 = GotongRoyong::whereIn('status', ['Disetujui2'])->count();
        } elseif (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan (role 2)
                // Ambil data desa (role 1) di kecamatan yang sama
                $desaIds = Pengguna::where('id_subdistrict', $user->id_subdistrict)
                    ->where('id_role', 1)
                    ->pluck('id');

                // Tampilkan data dengan status proses dan Disetujui1 untuk desa tersebut
                $got1 = GotongRoyong::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['proses'])
                    ->count();
                $got2 = GotongRoyong::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Disetujui1'])
                    ->count();
            } else {
                // Untuk role lainnya (jika ada)
                $got1 = 0;
                $got2 = 0;
            }
        } else {
            $got1 = 0;
            $got2 = 0;
        }

        return view('backend.accgotongroyong', compact('got1', 'got2'));
    }
}
