<?php

namespace App\Http\Controllers\backend;

use App\Models\Perencanaansehat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecPerencanaanController extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan semua data yang sudah disetujui
            $data2 = DB::table('laporan_perencanaan_sehat')
                ->join('users_mobile', 'laporan_perencanaan_sehat.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select('laporan_perencanaan_sehat.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                ->where('laporan_perencanaan_sehat.status', 'Disetujui2')
                ->orderBy('id_pokja4_bidang3', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika pengguna mobile dengan role 2 (Kecamatan)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Ambil data desa (role 1) di kecamatan tersebut yang sudah Disetujui1
                $data2 = DB::table('laporan_perencanaan_sehat')
                    ->join('users_mobile', 'laporan_perencanaan_sehat.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict) // Kecamatan yang sama
                    ->where('laporan_perencanaan_sehat.status', 'Disetujui1')
                    ->select('laporan_perencanaan_sehat.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->orderBy('id_pokja4_bidang3', 'desc')
                    ->get();
            } else {
                // Jika bukan role 2, kembalikan data kosong
                $data2 = collect();
            }
        } else {
            // Jika tidak ada guard yang cocok, kembalikan data kosong
            $data2 = collect();
        }

        return view('backend.decperencanaan', compact('data2'));
    }

    public function destroy(string $id_pokja4_bidang3)
    {
        $data2 = Perencanaansehat::find($id_pokja4_bidang3);

        if (!$data2) {
            return redirect()->route('decperencanaan.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();
        return redirect()->route('decperencanaan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
