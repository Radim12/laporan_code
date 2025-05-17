<?php

namespace App\Http\Controllers\backend;

use App\Models\Sandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SandangController extends Controller
{
    public function index()
    {
        // Check active guard
        if (Auth::guard('pengguna')->check()) {
            // For pengguna guard (mobile) - role kecamatan, show process data
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_sandang')
                ->join('users_mobile', 'laporan_sandang.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_sandang.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1) // Desa role
                ->where('laporan_sandang.status', 'Proses')
                ->orderBy('laporan_sandang.id_pokja3_bidang2', 'desc')
                ->get();
        } else {
            // For web guard - show approved data (Disetujui1)
            $data = DB::table('laporan_sandang')
                ->join('users_mobile', 'laporan_sandang.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_sandang.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_sandang.status', 'Disetujui1')
                ->orderBy('laporan_sandang.id_pokja3_bidang2', 'desc')
                ->get();
        }

        return view('backend.sandang', compact('data'));
    }

    public function edit(string $id_pokja3_bidang2)
    {
        $data = Sandang::find($id_pokja3_bidang2);
        return view('backend.tampil_sandang', compact('data'));
    }

    public function update(Request $request, string $id_pokja3_bidang2)
    {
        $data = Sandang::find($id_pokja3_bidang2);

        // Determine approval status based on guard
        $status = $request->status;
        if ($status == 'Disetujui') {
            if (Auth::guard('pengguna')->check()) {
                $status = 'Disetujui1'; // Status for mobile users
            } else {
                $status = 'Disetujui2'; // Status for web
            }
        }

        $data->update([
            'pangan' => $request->pangan,
            'sandang' => $request->sandang,
            'jasa' => $request->jasa,
            'status' => $status,
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        return redirect()->route('sandang.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function destroy(string $id_pokja3_bidang2)
    {
        $data = Sandang::find($id_pokja3_bidang2);
        $data->delete();
        return redirect()->route('sandang.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
