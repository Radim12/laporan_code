<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\LaporanPokja1;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LaporanPokja1Controller extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                // Ambil data desa (role 1) di kecamatan yang sama
                $data = DB::table('laporan_kader_pokja1')
                    ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select(
                        'laporan_kader_pokja1.*',
                        'subdistrict.name as nama_kec',
                        'village.name as nama_desa'
                    )
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->whereIn('laporan_kader_pokja1.status', ['proses'])
                    ->orderBy('laporan_kader_pokja1.id_kader_pokja1', 'desc')
                    ->get();
            }
        } else { // Guard web (admin)
            $data = DB::table('laporan_kader_pokja1')
                ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_kader_pokja1.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->whereIn('laporan_kader_pokja1.status', ['Disetujui1'])
                ->orderBy('laporan_kader_pokja1.id_kader_pokja1', 'desc')
                ->get();
        }

        return view('backend.laporanpokja1', compact('data'));
    }

    public function edit(string $id_kader_pokja1)
    {
        $data = LaporanPokja1::find($id_kader_pokja1);
        return view('backend.tampil_laporanpokja1', compact('data'));
    }

    public function update(Request $request, string $id_kader_pokja1)
    {
        $data = LaporanPokja1::find($id_kader_pokja1);

        // ✅ Tentukan status persetujuan berdasarkan guard
        $status = $request->status;
        if ($status == 'disetujui') {
            if (Auth::guard('pengguna')->check()) {
                $status = 'Disetujui1'; // Status untuk pengguna mobile
            } else {
                $status = 'Disetujui2'; // Status untuk web
            }
        }

        $data->update([
            'PKBN' => $request->PKBN,
            'PKDRT' => $request->PKDRT,
            'pola_asuh' => $request->pola_asuh,
            'id_user' => $request->id_user,
            'status' => $status, // <- gunakan hasil dari logika di atas
            'catatan' => $request->catatan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('laporanpokja1.index')->with(['success' => 'Berhasil Mengubah Status']);
    }


    public function destroy(string $id_kader_pokja1)
    {
        $data = LaporanPokja1::find($id_kader_pokja1);
        $data->delete();
        return redirect()->route('laporanpokja1.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
