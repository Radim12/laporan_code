<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Sandang;
use App\Models\Pangan;
use App\Models\Perumahan;
use App\Models\LaporanPokja3;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;

class Pokja3Controller extends Controller
{
    public function index()
    {
        // Cek guard yang sedang login
        if (Auth::guard('web')->check()) {
            // Jika guard web (admin)
            $modelPertama = Pangan::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKedua = Sandang::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKetiga = Perumahan::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKeempat = LaporanPokja3::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan (role 2)
                // Ambil semua desa (role 1) di kecamatan tersebut
                $desaIds = Pengguna::where('id_subdistrict', $user->id_subdistrict)
                    ->where('id_role', 1)
                    ->pluck('id');

                $modelPertama = Pangan::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Disetujui1', 'proses'])
                    ->count();

                $modelKedua = Sandang::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Disetujui1', 'proses'])
                    ->count();

                $modelKetiga = Perumahan::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Disetujui1', 'proses'])
                    ->count();

                $modelKeempat = LaporanPokja3::whereIn('id_user', $desaIds)
                    ->whereIn('status', ['Disetujui1', 'proses'])
                    ->count();
            }
        }

        return view('backend.pokja3', compact('modelPertama', 'modelKedua', 'modelKetiga', 'modelKeempat'));
    }
}
