<?php

namespace App\Http\Controllers\backend;

use App\Models\LaporanPokja4;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LaporanPokja4Controller extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_kader_pokja4')
                ->join('users_mobile', 'laporan_kader_pokja4.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kader_pokja4.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_kader_pokja4.status', 'Proses')
                ->orderBy('laporan_kader_pokja4.id_kader_pokja4', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_kader_pokja4')
                ->join('users_mobile', 'laporan_kader_pokja4.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kader_pokja4.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_kader_pokja4.status', 'Disetujui1')
                ->orderBy('laporan_kader_pokja4.id_kader_pokja4', 'desc')
                ->get();
        }

        return view('backend.laporanpokja4', compact('data'));
    }

    public function edit(string $id_kader_pokja4)
    {
        $data = LaporanPokja4::find($id_kader_pokja4);
        return view('backend.tampil_laporanpokja4', compact('data'));
    }

    public function update(Request $request, string $id_kader_pokja4)
    {
        $data = LaporanPokja4::find($id_kader_pokja4);

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
            'posyandu' => $request->posyandu,
            'gizi' => $request->gizi,
            'kesling' => $request->kesling,
            'penyuluhan_narkoba' => $request->penyuluhan_narkoba,
            'PHBS' => $request->PHBS,
            'KB' => $request->KB,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('laporanpokja4.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_kader_pokja4)
    {
        $data = LaporanPokja4::find($id_kader_pokja4);
        $data->delete();
        return redirect()->route('laporanpokja4.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
