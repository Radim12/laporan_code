<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Kesehatan;
use App\Models\KelestarianLingkunganHidup;
use App\Models\PerencanaanSehat;
use App\Models\LaporanPokja4;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;

class Pokja4Controller extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            // Jika pengguna adalah kecamatan (role 2)
            if ($user->id_role == 2) {
                // Ambil data desa (role 1) di kecamatan yang sama
                $desaIds = Pengguna::where('id_subdistrict', $user->id_subdistrict)
                    ->where('id_role', 1)
                    ->pluck('id');

                // Hitung data kesehatan dari desa-desa tersebut
                $modelPertama = Kesehatan::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Proses', 'Disetujui1'])
                    ->count();

                $modelKedua = KelestarianLingkunganHidup::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Proses', 'Disetujui1'])
                    ->count();

                $modelKetiga = PerencanaanSehat::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Proses', 'Disetujui1'])
                    ->count();

                $modelKeempat = LaporanPokja4::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Proses', 'Disetujui1'])
                    ->count();
            }
        } else {
            // Jika guard web - tampilkan data Disetujui1 dan Disetujui2
            $modelPertama = Kesehatan::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKedua = KelestarianLingkunganHidup::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKetiga = PerencanaanSehat::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKeempat = LaporanPokja4::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
        }

        return view('backend.pokja4', compact('modelPertama', 'modelKedua', 'modelKetiga', 'modelKeempat'));
    }
}
