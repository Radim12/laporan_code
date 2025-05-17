<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\LaporanPokja4;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccLaporanPokja4Controller extends Controller
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

                // Hitung data laporan pokja4 dari desa-desa tersebut
                $lap1 = LaporanPokja4::whereIn('id_user', $desaUsers)
                    ->where('status', 'proses')
                    ->count();

                $lap2 = LaporanPokja4::whereIn('id_user', $desaUsers)
                    ->where('status', 'Disetujui1')
                    ->count();
            }
        } else {
            // Jika guard web (admin) - tampilkan semua data
            $lap1 = LaporanPokja4::where('status', 'Disetujui1')->count();
            $lap2 = LaporanPokja4::where('status', 'Disetujui2')->count();
        }

        return view('backend.acclaporanpokja4', compact('lap1', 'lap2'));
    }
}
