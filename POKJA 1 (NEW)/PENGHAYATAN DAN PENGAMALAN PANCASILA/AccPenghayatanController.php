<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Penghayatan;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccPenghayatanController extends Controller
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

                // Hitung data penghayatan dari desa-desa tersebut
                $peng1 = Penghayatan::whereIn('id_user', $desaUsers)
                    ->where('status', 'proses')
                    ->count();

                $peng2 = Penghayatan::whereIn('id_user', $desaUsers)
                    ->where('status', 'Disetujui1')
                    ->count();
            }
        } else {
            // Jika guard web (admin) - tampilkan semua data
            $peng1 = Penghayatan::where('status', 'Disetujui1')->count();
            $peng2 = Penghayatan::where('status', 'Disetujui2')->count();
        }

        return view('backend.accpenghayatan', compact('peng1', 'peng2'));
    }
}
