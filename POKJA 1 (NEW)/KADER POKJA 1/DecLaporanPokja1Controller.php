<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\LaporanPokja1;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecLaporanPokja1Controller extends Controller
{
    public function index()
    {
        // Check if user is authenticated via web guard (admin)
        if (Auth::guard('web')->check()) {
            // For web guard (admin), show approved reports (Disetujui2)
            $data2 = DB::table('laporan_kader_pokja1')
                ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kader_pokja1.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kader_pokja1.status', 'Disetujui2')
                ->orderBy('id_kader_pokja1', 'desc')
                ->get();
        }
        // Check if user is authenticated via pengguna guard (mobile user)
        elseif (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            // If user is kecamatan (role 2), show reports from desa (role 1) in the same kecamatan
            if ($user->id_role == 2) {
                $data2 = DB::table('laporan_kader_pokja1')
                    ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->select('laporan_kader_pokja1.*', 'subdistrict.name as nama_kec')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Desa
                    ->where('laporan_kader_pokja1.status', 'Disetujui1')
                    ->orderBy('id_kader_pokja1', 'desc')
                    ->get();
            }
            // If user is desa (role 1), show only their own reports
            else {
                $data2 = DB::table('laporan_kader_pokja1')
                    ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->select('laporan_kader_pokja1.*', 'subdistrict.name as nama_kec')
                    ->where('laporan_kader_pokja1.id_user', $user->id)
                    ->where('laporan_kader_pokja1.status', 'Disetujui1')
                    ->orderBy('id_kader_pokja1', 'desc')
                    ->get();
            }
        }
        // If not authenticated, show empty data
        else {
            $data2 = collect();
        }

        return view('backend.declaporanpokja1', compact('data2'));
    }

    public function destroy(string $id_kader_pokja1)
    {
        $data2 = LaporanPokja1::find($id_kader_pokja1);
        $data2->delete();
        return redirect()->route('declaporanpokja1.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
