<?php

namespace App\Http\Controllers\backend;

use App\Models\BidangUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecBidangUmumController extends Controller
{
    public function index()
    {
        // Cek guard terlebih dahulu
        if (Auth::guard('web')->check()) {
            // Jika guard web (admin), tampilkan data dengan status 'Disetujui2'
            $data = DB::table('laporan_umum')
                ->join('users_mobile', 'laporan_umum.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_umum.*', 'subdistrict.name as nama_kec')
                ->where('laporan_umum.status', 'Disetujui2')
                ->orderBy('id_laporan_umum', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Jika role kecamatan (2), tampilkan data desa (role 1) di kecamatan tersebut dengan status 'Disetujui1'
                $data = DB::table('laporan_umum')
                    ->join('users_mobile', 'laporan_umum.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->select('laporan_umum.*', 'subdistrict.name as nama_kec')
                    ->where('laporan_umum.status', 'Disetujui1')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Desa
                    ->orderBy('id_laporan_umum', 'desc')
                    ->get();
            }
        } else {
            // Default jika tidak ada guard yang cocok
            $data = collect(); // Mengembalikan collection kosong
        }

        return view('backend.decbidangumum', compact('data'));
    }

    public function destroy(string $id_laporan_umum)
    {
        $data = BidangUmum::find($id_laporan_umum);
        $data->delete();

        return redirect()->route('decbidangumum.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
