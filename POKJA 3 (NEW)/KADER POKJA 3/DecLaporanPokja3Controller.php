<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\LaporanPokja3;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecLaporanPokja3Controller extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan data yang sudah disetujui (status = 'Disetujui2')
            $data2 = DB::table('laporan_kader_pokja3')
                ->join('users_mobile', 'laporan_kader_pokja3.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select('laporan_kader_pokja3.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                ->where('laporan_kader_pokja3.status', 'Disetujui2')
                ->orderBy('id_kader_pokja3', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika pengguna mobile
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Jika role kecamatan (2), tampilkan data semua desa di kecamatan tersebut yang sudah Disetujui1
                $data2 = DB::table('laporan_kader_pokja3')
                    ->join('users_mobile', 'laporan_kader_pokja3.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select('laporan_kader_pokja3.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Hanya data dari desa
                    ->where('laporan_kader_pokja3.status', 'Disetujui1')
                    ->orderBy('id_kader_pokja3', 'desc')
                    ->get();
            }
        }

        return view('backend.declaporanpokja3', compact('data2'));
    }

    public function destroy(string $id_kader_pokja3)
    {
        $data2 = LaporanPokja3::find($id_kader_pokja3);

        if (!$data2) {
            return redirect()->route('declaporanpokja3.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();

        return redirect()->route('declaporanpokja3.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
