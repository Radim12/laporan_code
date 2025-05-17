<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\PerencanaanSehat;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccperencanaanController extends Controller
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

                // Hitung data perencanaan sehat dari desa-desa tersebut
                $per1 = PerencanaanSehat::whereIn('id_user', $desaUsers)
                    ->where('status', 'proses')
                    ->count();

                $per2 = PerencanaanSehat::whereIn('id_user', $desaUsers)
                    ->where('status', 'Disetujui1')
                    ->count();
            }
        } else {
            // Jika guard web (admin) - tampilkan semua data
            $per1 = PerencanaanSehat::where('status', 'Disetujui1')->count();
            $per2 = PerencanaanSehat::where('status', 'Disetujui2')->count();
        }

        return view('backend.accperencanaan', compact('per1', 'per2'));
    }
}
