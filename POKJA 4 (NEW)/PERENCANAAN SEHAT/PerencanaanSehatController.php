<?php

namespace App\Http\Controllers\backend;

use App\Models\PerencanaanSehat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PerencanaanSehatController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_perencanaan_sehat')
                ->join('users_mobile', 'laporan_perencanaan_sehat.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_perencanaan_sehat.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_perencanaan_sehat.status', 'Proses')
                ->orderBy('laporan_perencanaan_sehat.id_pokja4_bidang3', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_perencanaan_sehat')
                ->join('users_mobile', 'laporan_perencanaan_sehat.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_perencanaan_sehat.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_perencanaan_sehat.status', 'Disetujui1')
                ->orderBy('laporan_perencanaan_sehat.id_pokja4_bidang3', 'desc')
                ->get();
        }

        return view('backend.perencanaan_sehat', compact('data'));
    }

    public function edit(string $id_pokja4_bidang3)
    {
        $data = PerencanaanSehat::find($id_pokja4_bidang3);
        return view('backend.tampil_perencanaan_sehat', compact('data'));
    }

    public function update(Request $request, string $id_pokja4_bidang3)
    {
        $data = PerencanaanSehat::find($id_pokja4_bidang3);

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
            'J_Psubur' => $request->J_Psubur,
            'J_Wsubur' => $request->J_Wsubur,
            'Kb_p' => $request->Kb_p,
            'Kb_w' => $request->Kb_w,
            'Kk_tbg' => $request->Kk_tbg,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('perencanaan_sehat.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_pokja4_bidang3)
    {
        $data = PerencanaanSehat::find($id_pokja4_bidang3);
        $data->delete();
        return redirect()->route('perencanaan_sehat.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
