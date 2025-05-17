<?php

namespace App\Http\Controllers\backend;

use App\Models\Pangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DecPanganController extends Controller
{
    public function index()
    {
        if (Auth::guard('web')->check()) {
            // Jika yang login adalah admin (guard web)
            $data2 = DB::table('laporan_pangan')
                ->join('users_mobile', 'laporan_pangan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pangan.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                ->where('laporan_pangan.status', 'Disetujui2')
                ->orderBy('id_pokja3_bidang1', 'desc')
                ->get();
        } elseif (Auth::guard('pengguna')->check()) {
            // Jika yang login adalah pengguna (guard pengguna)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                $data2 = DB::table('laporan_pangan')
                    ->join('users_mobile', 'laporan_pangan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select(
                        'laporan_pangan.*',
                        'subdistrict.name as nama_kec',
                        'village.name as nama_desa'
                    )
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->where('laporan_pangan.status', 'Disetujui1')
                    ->orderBy('id_pokja3_bidang1', 'desc')
                    ->get();
            }
        } else {
            // Jika tidak ada yang login
            $data2 = collect(); // Mengembalikan collection kosong
        }

        return view('backend.decpangan', compact('data2'));
    }

    public function destroy(string $id_pokja3_bidang1)
    {
        $data2 = Pangan::find($id_pokja3_bidang1);

        if (!$data2) {
            return redirect()->route('decpangan.index')->with(['error' => 'Data tidak ditemukan']);
        }

        $data2->delete();

        return redirect()->route('decpangan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
