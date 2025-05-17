<?php

namespace App\Http\Controllers\backend;

use App\Models\Perumahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PerumahanController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_perumahan')
                ->join('users_mobile', 'laporan_perumahan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_perumahan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_perumahan.status', 'Proses')
                ->orderBy('laporan_perumahan.id_pokja3_bidang3', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_perumahan')
                ->join('users_mobile', 'laporan_perumahan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_perumahan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_perumahan.status', 'Disetujui1')
                ->orderBy('laporan_perumahan.id_pokja3_bidang3', 'desc')
                ->get();
        }

        return view('backend.perumahan', compact('data'));
    }

    public function edit(string $id_pokja3_bidang3)
    {
        $data = Perumahan::find($id_pokja3_bidang3);
        return view('backend.tampil_perumahan', compact('data'));
    }

    public function update(Request $request, string $id_pokja3_bidang3)
    {
        $data = Perumahan::find($id_pokja3_bidang3);

        $status = $request->status;
        if ($status == 'Disetujui') {
            if (Auth::guard('pengguna')->check()) {
                $status = 'Disetujui1'; // Status untuk pengguna mobile
            } else {
                $status = 'Disetujui2'; // Status untuk web
            }
        }

        $data->update([
            'layak_huni' => $request->layak_huni,
            'tidak_layak' => $request->tidak_layak,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('perumahan.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_pokja3_bidang3)
    {
        $data = Perumahan::find($id_pokja3_bidang3);
        $data->delete();
        return redirect()->route('perumahan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}