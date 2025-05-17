<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Perumahan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccPerumahanController extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan data dengan status Disetujui1 dan Disetujui2
            $per1 = Perumahan::where('status', 'Disetujui1')->count();
            $per2 = Perumahan::where('status', 'Disetujui2')->count();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika pengguna mobile
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan (role 2)
                // Ambil data dari desa (role 1) di kecamatan yang sama
                $per1 = Perumahan::whereHas('user', function ($query) use ($user) {
                    $query->where('id_role', 1)
                        ->where('id_subdistrict', $user->id_subdistrict);
                })->where('status', 'proses')->count();

                $per2 = Perumahan::whereHas('user', function ($query) use ($user) {
                    $query->where('id_role', 1)
                        ->where('id_subdistrict', $user->id_subdistrict);
                })->where('status', 'Disetujui1')->count();
            }
        }

        return view('backend.accperumahan', compact('per1', 'per2'));
    }
}
