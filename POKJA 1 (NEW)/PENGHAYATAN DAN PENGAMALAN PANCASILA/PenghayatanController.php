<?php

namespace App\Http\Controllers\backend;

use App\Models\Penghayatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PenghayatanController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_penghayatan_n_pengamalan')
                ->join('users_mobile', 'laporan_penghayatan_n_pengamalan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_penghayatan_n_pengamalan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_penghayatan_n_pengamalan.status', 'Proses')
                ->orderBy('laporan_penghayatan_n_pengamalan.id_pokja1_bidang1', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_penghayatan_n_pengamalan')
                ->join('users_mobile', 'laporan_penghayatan_n_pengamalan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_penghayatan_n_pengamalan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_penghayatan_n_pengamalan.status', 'Disetujui1')
                ->orderBy('laporan_penghayatan_n_pengamalan.id_pokja1_bidang1', 'desc')
                ->get();
        }

        return view('backend.penghayatan', compact('data'));
    }

    public function edit(string $id_pokja1_bidang1)
    {
        $data = Penghayatan::find($id_pokja1_bidang1);
        return view('backend.tampil_penghayatan', compact('data'));
    }

    public function update(Request $request, string $id_pokja1_bidang1)
    {
        $data = Penghayatan::find($id_pokja1_bidang1);

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
            'jumlah_kel_simulasi1' => $request->jumlah_kel_simulasi1,
            'jumlah_anggota1' => $request->jumlah_anggota1,
            'jumlah_kel_simulasi2' => $request->jumlah_kel_simulasi2,
            'jumlah_anggota2' => $request->jumlah_anggota2,
            'jumlah_kel_simulasi3' => $request->jumlah_kel_simulasi3,
            'jumlah_anggota3' => $request->jumlah_anggota3,
            'jumlah_kel_simulasi4' => $request->jumlah_kel_simulasi4,
            'jumlah_anggota4' => $request->jumlah_anggota4,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('penghayatan.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_pokja1_bidang1)
    {
        $data = Penghayatan::find($id_pokja1_bidang1);
        $data->delete();
        return redirect()->route('penghayatan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
