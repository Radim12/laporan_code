<?php

namespace App\Http\Controllers\backend;

use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecPendidikanController extends Controller
{
    public function index()
    {
        // Cek guard yang digunakan
        if (Auth::guard('web')->check()) {
            // Jika guard web (admin), tampilkan semua data yang disetujui
            $data2 = DB::table('laporan_pendidikan_n_keterampilan')
                ->join('users_mobile', 'laporan_pendidikan_n_keterampilan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pendidikan_n_keterampilan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pendidikan_n_keterampilan.status', 'Disetujui2')
                ->orderBy('id_pokja2_bidang1', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                // Tampilkan data desa (role 1) di kecamatan tersebut yang Disetujui1
                $data2 = DB::table('laporan_pendidikan_n_keterampilan')
                    ->join('users_mobile', 'laporan_pendidikan_n_keterampilan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select('laporan_pendidikan_n_keterampilan.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Desa
                    ->where('laporan_pendidikan_n_keterampilan.status', 'Disetujui1')
                    ->orderBy('id_pokja2_bidang1', 'desc')
                    ->get();
            }
        } else {
            // Jika tidak ada guard yang valid, kembalikan array kosong
            $data2 = [];
        }

        return view('backend.decpendidikan', compact('data2'));
    }

    public function destroy(string $id_pokja2_bidang1)
    {
        $data2 = Pendidikan::find($id_pokja2_bidang1);

        if (!$data2) {
            return redirect()->route('decpendidikan.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();

        return redirect()->route('decpendidikan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
