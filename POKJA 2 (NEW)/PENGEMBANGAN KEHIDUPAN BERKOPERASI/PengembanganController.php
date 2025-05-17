<?php

namespace App\Http\Controllers\backend;

use App\Models\Pengembangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PengembanganController extends Controller
{
    public function index()
    {
        // Cek guard yang digunakan
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan data dengan status 'proses'
            $data = DB::table('laporan_pengembangan_kehidupan')
                ->join('users_mobile', 'laporan_pengembangan_kehidupan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pengembangan_kehidupan.*', 'subdistrict.name as nama_kec')
                ->whereIn('laporan_pengembangan_kehidupan.status', ['Disetujui1'])
                ->orderBy('id_pokja2_bidang2', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                // Tampilkan data dari desa (role 1) di kecamatan yang sama
                $data = DB::table('laporan_pengembangan_kehidupan')
                    ->join('users_mobile', 'laporan_pengembangan_kehidupan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select('laporan_pengembangan_kehidupan.*', 'village.name as nama_desa', 'subdistrict.name as nama_kec')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Desa
                    ->whereIn('laporan_pengembangan_kehidupan.status', ['proses'])
                    ->orderBy('id_pokja2_bidang2', 'desc')
                    ->get();
            }
        }

        return view('backend.pengembangan', compact('data'));
    }

    public function edit(string $id_pokja2_bidang2)
    {
        $data = Pengembangan::find($id_pokja2_bidang2);
        return view('backend.tampil_pengembangan', compact('data'));
    }

    public function update(Request $request, string $id_pokja2_bidang2)
    {
        $data = Pengembangan::find($id_pokja2_bidang2);
        $data->update([
            'jumlah_kelompok_pemula' => $request->jumlah_kelompok_pemula,
            'jumlah_peserta_pemula' => $request->jumlah_peserta_pemula,
            'jumlah_kelompok_madya' => $request->jumlah_kelompok_madya,
            'jumlah_peserta_madya' => $request->jumlah_peserta_madya,
            'jumlah_kelompok_utama' => $request->jumlah_kelompok_utama,
            'jumlah_peserta_utama' => $request->jumlah_peserta_utama,
            'jumlah_kelompok_mandiri' => $request->jumlah_kelompok_mandiri,
            'jumlah_peserta_mandiri' => $request->jumlah_peserta_mandiri,
            'jumlah_kelompok_hukum' => $request->jumlah_kelompok_hukum,
            'jumlah_peserta_hukum' => $request->jumlah_peserta_hukum,
            'id_user' => $request->id_user,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('pengembangan.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_pokja2_bidang2)
    {
        $data = Pengembangan::find($id_pokja2_bidang2);
        $data->delete();
        return redirect()->route('pengembangan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
