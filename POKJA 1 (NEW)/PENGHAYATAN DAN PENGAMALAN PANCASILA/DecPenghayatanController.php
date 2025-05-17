<?php

namespace App\Http\Controllers\backend;

use App\Models\Penghayatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecPenghayatanController extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan semua data yang sudah disetujui
            $data2 = DB::table('laporan_penghayatan_n_pengamalan')
                ->join('users_mobile', 'laporan_penghayatan_n_pengamalan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_penghayatan_n_pengamalan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_penghayatan_n_pengamalan.status', 'Disetujui2')
                ->orderBy('id_pokja1_bidang1', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika pengguna mobile dengan role 2 (Kecamatan)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Ambil data desa (role 1) di kecamatan tersebut yang sudah Disetujui1
                $data2 = DB::table('laporan_penghayatan_n_pengamalan')
                    ->join('users_mobile', 'laporan_penghayatan_n_pengamalan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict) // Kecamatan yang sama
                    ->where('laporan_penghayatan_n_pengamalan.status', 'Disetujui1')
                    ->select('laporan_penghayatan_n_pengamalan.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->orderBy('id_pokja1_bidang1', 'desc')
                    ->get();
            } else {
                // Jika bukan role 2, kembalikan data kosong
                $data2 = collect();
            }
        } else {
            // Jika tidak ada guard yang cocok, kembalikan data kosong
            $data2 = collect();
        }

        return view('backend.decpenghayatan', compact('data2'));
    }

    public function destroy(string $id_pokja1_bidang1)
    {
        $data2 = Penghayatan::find($id_pokja1_bidang1);
        $data2->delete();
        return redirect()->route('decpenghayatan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
