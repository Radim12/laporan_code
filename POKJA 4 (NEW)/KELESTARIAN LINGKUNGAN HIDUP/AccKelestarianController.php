<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\KelestarianLingkunganHidup;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccKelestarianController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan (role 2)
                // Ambil semua user mobile dengan role 1 (Desa) di kecamatan yang sama
                $desaUsers = Pengguna::where('id_subdistrict', $user->id_subdistrict)
                    ->where('id_role', 1)
                    ->pluck('id');

                // Hitung data kelestarian dari desa-desa tersebut
                $kel1 = KelestarianLingkunganHidup::whereIn('id_user', $desaUsers)
                    ->where('status', 'proses')
                    ->count();

                $kel2 = KelestarianLingkunganHidup::whereIn('id_user', $desaUsers)
                    ->where('status', 'Disetujui1')
                    ->count();
            }
        } else {
            // Jika guard web (admin) - tampilkan semua data
            $kel1 = KelestarianLingkunganHidup::where('status', 'Disetujui1')->count();
            $kel2 = KelestarianLingkunganHidup::where('status', 'Disetujui2')->count();
        }

        return view('backend.acckelestarian', compact('kel1', 'kel2'));
    }
}
