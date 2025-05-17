<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Pendidikan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccPendidikanController extends Controller
{
    public function index()
    {
        // Cek guard yang digunakan
        if (Auth::guard('web')->check()) {
            // Jika guard web (admin)
            $pend1 = Pendidikan::where('status', 'Disetujui1')->count();
            $pend2 = Pendidikan::where('status', 'Disetujui2')->count();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                // Ambil data dari desa (role 1) di kecamatan yang sama
                $pend1 = Pendidikan::whereHas('user', function ($query) use ($user) {
                    $query->where('id_role', 1)
                        ->where('id_subdistrict', $user->id_subdistrict);
                })->where('status', 'proses')->count();

                $pend2 = Pendidikan::whereHas('user', function ($query) use ($user) {
                    $query->where('id_role', 1)
                        ->where('id_subdistrict', $user->id_subdistrict);
                })->where('status', 'Disetujui1')->count();
            }
        }

        return view('backend.accpendidikan', compact('pend1', 'pend2'));
    }
}
