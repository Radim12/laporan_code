<?php

namespace App\Http\Controllers\backend;

use App\Models\KelestarianLingkunganHidup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KelestarianLingkunganHidupController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_kelestarian_lingkungan_hidup')
                ->join('users_mobile', 'laporan_kelestarian_lingkungan_hidup.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kelestarian_lingkungan_hidup.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_kelestarian_lingkungan_hidup.status', 'Proses')
                ->orderBy('laporan_kelestarian_lingkungan_hidup.id_pokja4_bidang2', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_kelestarian_lingkungan_hidup')
                ->join('users_mobile', 'laporan_kelestarian_lingkungan_hidup.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kelestarian_lingkungan_hidup.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_kelestarian_lingkungan_hidup.status', 'Disetujui1')
                ->orderBy('laporan_kelestarian_lingkungan_hidup.id_pokja4_bidang2', 'desc')
                ->get();
        }

        return view('backend.kelestarian_lingkungan_hidup', compact('data'));
    }

    public function edit(string $id_pokja4_bidang2)
    {
        $data = KelestarianLingkunganHidup::find($id_pokja4_bidang2);
        return view('backend.tampil_kelestarian_lingkungan_hidup', compact('data'));
    }

    public function update(Request $request, string $id_pokja4_bidang2)
    {
        $data = KelestarianLingkunganHidup::find($id_pokja4_bidang2);

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
            'jamban' => $request->jamban,
            'spal' => $request->spal,
            'tps' => $request->tps,
            'mck' => $request->mck,
            'pdam' => $request->pdam,
            'sumur' => $request->sumur,
            'dll' => $request->dll,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('kelestarian_lingkungan_hidup.index')
            ->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_pokja4_bidang2)
    {
        $data = KelestarianLingkunganHidup::find($id_pokja4_bidang2);
        $data->delete();

        return redirect()->route('kelestarian_lingkungan_hidup.index')
            ->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
