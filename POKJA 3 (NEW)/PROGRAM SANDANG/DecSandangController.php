<?php

namespace App\Http\Controllers\backend;

use App\Models\Sandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecSandangController extends Controller
{
    public function index()
    {
        if (Auth::guard('web')->check()) {
            // Jika admin (guard web), tampilkan semua data yang statusnya 'Disetujui2'
            $data2 = DB::table('laporan_sandang')
                ->join('users_mobile', 'laporan_sandang.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_sandang.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_sandang.status', 'Disetujui2')
                ->orderBy('id_pokja3_bidang2', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika pengguna kecamatan (guard pengguna), tampilkan data desa di kecamatan tersebut yang statusnya 'Disetujui1'
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                $data2 = DB::table('laporan_sandang')
                    ->join('users_mobile', 'laporan_sandang.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select('laporan_sandang.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Desa
                    ->where('laporan_sandang.status', 'Disetujui1')
                    ->orderBy('id_pokja3_bidang2', 'desc')
                    ->get();
            }
        } else {
            // Jika tidak ada guard yang cocok, kosongkan data
            $data2 = [];
        }

        return view('backend.decsandang', compact('data2'));
    }

    public function destroy(string $id_pokja3_bidang2)
    {
        $data2 = Sandang::find($id_pokja3_bidang2);

        if (!$data2) {
            return redirect()->route('decsandang.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();

        return redirect()->route('decsandang.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
