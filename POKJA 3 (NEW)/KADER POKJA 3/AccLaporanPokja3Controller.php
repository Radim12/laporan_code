<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\LaporanPokja3;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccLaporanPokja3Controller extends Controller
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

                // Hitung data pokja3 dari desa-desa tersebut
                $lap1 = LaporanPokja3::whereIn('id_user', $desaUsers)
                    ->where('status', 'proses')
                    ->count();

                $lap2 = LaporanPokja3::whereIn('id_user', $desaUsers)
                    ->where('status', 'Disetujui1')
                    ->count();

                // Ambil data untuk ditampilkan
                $data = LaporanPokja3::whereIn('id_user', $desaUsers)
                    ->whereIn('status', ['proses', 'Disetujui1'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } else {
            // Jika guard web (admin) - tampilkan semua data
            $lap1 = LaporanPokja3::where('status', 'Disetujui1')->count();
            $lap2 = LaporanPokja3::where('status', 'Disetujui2')->count();

            // Ambil data untuk ditampilkan
            $data = LaporanPokja3::whereIn('status', ['Disetujui1', 'Disetujui2'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('backend.acclaporanpokja3', compact('lap1', 'lap2', 'data'));
    }
}
