<?php

namespace App\Http\Controllers\backend;

use App\Models\Pengembangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecPengembanganController extends Controller
{
    public function index()
    {
        // Cek guard yang sedang login
        if (Auth::guard('web')->check()) {
            // Jika guard web (admin), tampilkan semua data yang disetujui
            $data2 = DB::table('laporan_pengembangan_kehidupan')
                ->join('users_mobile', 'laporan_pengembangan_kehidupan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_pengembangan_kehidupan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_pengembangan_kehidupan.status', 'Disetujui2')
                ->orderBy('id_pokja2_bidang2', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                // Tampilkan data desa (role 1) di kecamatan tersebut yang Disetujui1
                $data2 = DB::table('laporan_pengembangan_kehidupan')
                    ->join('users_mobile', 'laporan_pengembangan_kehidupan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select(
                        'laporan_pengembangan_kehidupan.*',
                        'subdistrict.name as nama_kec',
                        'village.name as nama_desa'
                    )
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Desa
                    ->where('laporan_pengembangan_kehidupan.status', 'Disetujui1')
                    ->orderBy('id_pokja2_bidang2', 'desc')
                    ->get();
            }
        } else {
            // Jika tidak ada guard yang login, kembalikan data kosong
            $data2 = collect();
        }

        return view('backend.decpengembangan', compact('data2'));
    }

    public function destroy(string $id_pokja2_bidang2)
    {
        $data2 = Pengembangan::find($id_pokja2_bidang2);

        // Tambahkan pengecekan hak akses sebelum menghapus
        if (
            Auth::guard('web')->check() ||
            (Auth::guard('pengguna')->check() && Auth::guard('pengguna')->user()->id_role == 2)
        ) {
            $data2->delete();
            return redirect()->route('decpengembangan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
        }

        return redirect()->route('decpengembangan.index')->with(['error' => 'Anda tidak memiliki izin untuk menghapus data ini']);
    }
}
