<?php

namespace App\Http\Controllers\backend;

use App\Models\LaporanPokja4;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecLaporanPokja4Controller extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan semua data yang sudah disetujui
            $data2 = DB::table('laporan_kader_pokja4')
                ->join('users_mobile', 'laporan_kader_pokja4.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kader_pokja4.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kader_pokja4.status', 'Disetujui2')
                ->orderBy('id_kader_pokja4', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika pengguna mobile dengan role 2 (Kecamatan)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Ambil data desa (role 1) di kecamatan tersebut yang sudah Disetujui1
                $data2 = DB::table('laporan_kader_pokja4')
                    ->join('users_mobile', 'laporan_kader_pokja4.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict) // Kecamatan yang sama
                    ->where('laporan_kader_pokja4.status', 'Disetujui1')
                    ->select('laporan_kader_pokja4.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->orderBy('id_kader_pokja4', 'desc')
                    ->get();
            } else {
                // Jika bukan role 2, kembalikan data kosong
                $data2 = collect();
            }
        } else {
            // Jika tidak ada guard yang cocok, kembalikan data kosong
            $data2 = collect();
        }

        return view('backend.declaporanpokja4', compact('data2'));
    }

    public function destroy(string $id_kader_pokja4)
    {
        $data2 = LaporanPokja4::find($id_kader_pokja4);

        if (!$data2) {
            return redirect()->route('declaporanpokja4.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();

        return redirect()->route('declaporanpokja4.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
