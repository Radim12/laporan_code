<?php

namespace App\Http\Controllers\backend;

use App\Models\Perumahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecPerumahanController extends Controller
{
    public function index()
    {
        if (Auth::guard('web')->check()) {
            // Untuk admin (guard web), tampilkan data dengan status 'Disetujui2'
            $data2 = DB::table('laporan_perumahan')
                ->join('users_mobile', 'laporan_perumahan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_perumahan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_perumahan.status', 'Disetujui2')
                ->orderBy('id_pokja3_bidang3', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Untuk kecamatan (role 2), tampilkan data desa (role 1) di kecamatan tersebut dengan status 'Disetujui1'
                $data2 = DB::table('laporan_perumahan')
                    ->join('users_mobile', 'laporan_perumahan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select('laporan_perumahan.*', 'village.name as nama_desa', 'subdistrict.name as nama_kec')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1)
                    ->where('laporan_perumahan.status', 'Disetujui1')
                    ->orderBy('id_pokja3_bidang3', 'desc')
                    ->get();
            }
        }

        return view('backend.decperumahan', compact('data2'));
    }

    public function destroy(string $id_pokja3_bidang3)
    {
        $data2 = Perumahan::find($id_pokja3_bidang3);

        if (!$data2) {
            return redirect()->route('decperumahan.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();

        return redirect()->route('decperumahan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
