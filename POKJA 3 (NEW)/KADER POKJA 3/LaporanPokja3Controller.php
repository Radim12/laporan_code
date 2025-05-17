<?php

namespace App\Http\Controllers\backend;

use App\Models\LaporanPokja3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LaporanPokja3Controller extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_kader_pokja3')
                ->join('users_mobile', 'laporan_kader_pokja3.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kader_pokja3.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_kader_pokja3.status', 'Proses')
                ->orderBy('laporan_kader_pokja3.id_kader_pokja3', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_kader_pokja3')
                ->join('users_mobile', 'laporan_kader_pokja3.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kader_pokja3.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_kader_pokja3.status', 'Disetujui1')
                ->orderBy('laporan_kader_pokja3.id_kader_pokja3', 'desc')
                ->get();
        }

        return view('backend.laporanpokja3', compact('data'));
    }

    public function edit(string $id_kader_pokja3)
    {
        $data = LaporanPokja3::find($id_kader_pokja3);

        // Tambahkan pengecekan akses
        if (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();
            if ($user->id_role == 1 && $data->id_user != $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('backend.tampil_laporanpokja3', compact('data'));
    }

    public function update(Request $request, string $id_kader_pokja3)
    {
        $data = LaporanPokja3::find($id_kader_pokja3);

        // Tambahkan pengecekan akses
        if (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();
            if ($user->id_role == 1 && $data->id_user != $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Tentukan status persetujuan berdasarkan guard
        $status = $request->status;
        if ($status == 'Disetujui') {
            if (Auth::guard('pengguna')->check()) {
                $status = 'Disetujui1'; // Status untuk pengguna mobile
            } else {
                $status = 'Disetujui2'; // Status untuk web
            }
        }

        $data->update([
            'pangan' => $request->pangan,
            'sandang' => $request->sandang,
            'tata_laksana_rumah' => $request->tata_laksana_rumah,
            'status' => $status,
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        return redirect()->route('laporanpokja3.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_kader_pokja3)
    {
        $data = LaporanPokja3::find($id_kader_pokja3);

        // Tambahkan pengecekan akses
        if (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();
            if ($user->id_role == 1 && $data->id_user != $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $data->delete();
        return redirect()->route('laporanpokja3.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
