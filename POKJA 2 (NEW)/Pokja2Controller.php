<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Pendidikan;
use App\Models\Pengembangan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Pokja2Controller extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('web')->check()) {
            // Untuk guard web (admin)
            $modelPertama = Pendidikan::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
            $modelKedua = Pengembangan::whereIn('status', ['Disetujui1', 'Disetujui2'])->count();
        } elseif (Auth::guard('pengguna')->check()) {
            // Untuk guard pengguna
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan (role 2)
                // Ambil data dari desa (role 1) di kecamatan yang sama
                $modelPertama = Pendidikan::whereHas('user', function ($query) use ($user) {
                    $query->where('id_role', 1)
                        ->where('id_subdistrict', $user->id_subdistrict);
                })->whereIn('status', ['Disetujui1', 'proses'])->count();

                $modelKedua = Pengembangan::whereHas('user', function ($query) use ($user) {
                    $query->where('id_role', 1)
                        ->where('id_subdistrict', $user->id_subdistrict);
                })->whereIn('status', ['Disetujui1', 'proses'])->count();
            }
        } else {
            // Default jika tidak ada guard yang aktif
            $modelPertama = 0;
            $modelKedua = 0;
        }

        return view('backend.pokja2', compact('modelPertama', 'modelKedua'));
    }
}
