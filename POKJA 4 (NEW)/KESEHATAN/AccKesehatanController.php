<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Kesehatan;
use App\Models\Pengguna;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccKesehatanController extends Controller
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

                // Hitung data kesehatan dari desa-desa tersebut
                $kes1 = Kesehatan::whereIn('id_user', $desaUsers)
                    ->where('status', 'proses')
                    ->count();

                $kes2 = Kesehatan::whereIn('id_user', $desaUsers)
                    ->where('status', 'Disetujui1')
                    ->count();
            } else {
                // Untuk role 1 (Desa)
                $kes1 = Kesehatan::where('id_user', $user->id)
                    ->where('status', 'proses')
                    ->count();

                $kes2 = Kesehatan::where('id_user', $user->id)
                    ->where('status', 'Disetujui1')
                    ->count();
            }
        } else {
            // Jika guard web (admin) - tampilkan semua data
            $kes1 = Kesehatan::where('status', 'Disetujui1')->count();
            $kes2 = Kesehatan::where('status', 'Disetujui2')->count();
        }

        return view('backend.acckesehatan', compact('kes1', 'kes2'));
    }
}
