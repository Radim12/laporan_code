<?php

namespace App\Http\Controllers\backend;

use Carbon\Carbon;
use App\Models\Ttd;
use App\Models\Ttds;
use App\Models\Pangan;
use App\Models\Sandang;
use App\Models\Perumahan;
use Illuminate\Http\Request;
use App\Models\LaporanPokja3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Pengguna;

class PanganController extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('web')->check()) {
            // Jika admin web, tampilkan semua data dengan status 'Disetujui1'
            $data = DB::table('laporan_pangan')
                ->join('users_mobile', 'laporan_pangan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select('laporan_pangan.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                ->where('laporan_pangan.status', 'Disetujui1')
                ->orderBy('id_pokja3_bidang1', 'desc')
                ->get();
        } elseif (Auth::guard('users_mobile')->check()) {
            $user = Auth::guard('users_mobile')->user();

            if ($user->id_role == 2) { // Kecamatan
                // Ambil data desa (role 1) di kecamatan tersebut dengan status 'proses'
                $data = DB::table('laporan_pangan')
                    ->join('users_mobile', 'laporan_pangan.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->join('village', 'users_mobile.id_village', '=', 'village.id')
                    ->select('laporan_pangan.*', 'subdistrict.name as nama_kec', 'village.name as nama_desa')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->where('laporan_pangan.status', 'Proses')
                    ->orderBy('id_pokja3_bidang1', 'desc')
                    ->get();
            }
        } else {
            $data = collect(); // Return empty collection jika tidak ada guard yang valid
        }

        return view('backend.pangan', compact('data'));
    }

    public function edit(string $id_pokja3_bidang1)
    {
        $data = Pangan::find($id_pokja3_bidang1);

        // Ambil data user terkait untuk menampilkan info kecamatan/desa
        $user = Pengguna::find($data->id_user);
        $kecamatan = DB::table('subdistrict')->where('id', $user->id_subdistrict)->first();
        $desa = DB::table('village')->where('id', $user->id_village)->first();

        return view('backend.tampil_pangan', compact('data', 'kecamatan', 'desa'));
    }

    public function update(Request $request, string $id_pokja3_bidang1)
    {
        $data = Pangan::find($id_pokja3_bidang1);

        // Tentukan status persetujuan berdasarkan guard
        $status = $request->status;
        if ($status == 'Disetujui') {
            if (Auth::guard('users_mobile')->check()) {
                $status = 'Disetujui1'; // Status untuk pengguna mobile (kecamatan)
            } else {
                $status = 'Disetujui2'; // Status untuk web
            }
        }

        $data->update([
            'beras' => $request->beras,
            'non_beras' => $request->non_beras,
            'peternakan' => $request->peternakan,
            'perikanan' => $request->perikanan,
            'warung_hidup' => $request->warung_hidup,
            'lumbung_hidup' => $request->lumbung_hidup,
            'toga' => $request->toga,
            'tanaman_keras' => $request->tanaman_keras,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pangan.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function filter(Request $request)
    {
        // Tentukan status berdasarkan guard yang aktif
        $status = Auth::guard('web')->check() ? 'Disetujui2' : 'Disetujui1';

        if ($request->has('search')) {
            // Query untuk laporan pangan
            $pangan = DB::table('laporan_pangan')
                ->join('users_mobile', 'laporan_pangan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pangan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pangan.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_pangan.status', $status)
                ->get();

            $total1 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('beras');
            $total2 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('non_beras');
            $total3 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('peternakan');
            $total31 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('perikanan');
            $total4 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('warung_hidup');
            $total5 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('lumbung_hidup');
            $total6 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('toga');
            $total7 = Pangan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('tanaman_keras');

            // Query untuk laporan sandang
            $sandang = DB::table('laporan_sandang')
                ->join('users_mobile', 'laporan_sandang.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_sandang.*', 'subdistrict.name as nama_kec')
                ->where('laporan_sandang.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_sandang.status', $status)
                ->get();

            $total24 = Sandang::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('pangan');
            $total25 = Sandang::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('sandang');
            $total26 = Sandang::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jasa');

            // Query untuk laporan perumahan
            $perumahan = DB::table('laporan_perumahan')
                ->join('users_mobile', 'laporan_perumahan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_perumahan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_perumahan.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_perumahan.status', $status)
                ->get();

            $total8 = Perumahan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('layak_huni');
            $total9 = Perumahan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('tidak_layak');

            // Query untuk laporan pokja3
            $laporanpokja3 = DB::table('laporan_kader_pokja3')
                ->join('users_mobile', 'laporan_kader_pokja3.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kader_pokja3.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kader_pokja3.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_kader_pokja3.status', $status)
                ->get();

            $total10 = LaporanPokja3::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('pangan');
            $total11 = LaporanPokja3::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('sandang');
            $total12 = LaporanPokja3::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('tata_laksana_rumah');

            // Cek data kosong
            $allTablesEmpty = $pangan->isEmpty() && $sandang->isEmpty() && $perumahan->isEmpty() && $laporanpokja3->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search');
            $carbonDate = Carbon::parse($created_at);
            $created_at = $carbonDate->isoFormat('MMMM YYYY');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja III')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_bulan_pokja3', compact(
                'pangan',
                'sandang',
                'perumahan',
                'laporanpokja3',
                'total1',
                'total2',
                'total3',
                'total4',
                'total5',
                'total6',
                'total7',
                'total8',
                'total9',
                'total10',
                'total11',
                'total12',
                'total24',
                'total25',
                'total26',
                'total31',
                'formattedDate',
                'ketua',
                'wakil',
                'created_at'
            ));
        } elseif ($request->has('search2')) {
            // Logika yang sama untuk filter tahun
            $pangan = DB::table('laporan_pangan')
                ->join('users_mobile', 'laporan_pangan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pangan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pangan.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_pangan.status', $status)
                ->get();

            $total1 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('beras');
            $total2 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('non_beras');
            $total3 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('peternakan');
            $total31 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('perikanan');
            $total4 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('warung_hidup');
            $total5 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('lumbung_hidup');
            $total6 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('toga');
            $total7 = Pangan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('tanaman_keras');

            $sandang = DB::table('laporan_sandang')
                ->join('users_mobile', 'laporan_sandang.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_sandang.*', 'subdistrict.name as nama_kec')
                ->where('laporan_sandang.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_sandang.status', $status)
                ->get();

            $total24 = Sandang::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('pangan');
            $total25 = Sandang::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('sandang');
            $total26 = Sandang::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jasa');

            $perumahan = DB::table('laporan_perumahan')
                ->join('users_mobile', 'laporan_perumahan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_perumahan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_perumahan.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_perumahan.status', $status)
                ->get();

            $total8 = Perumahan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('layak_huni');
            $total9 = Perumahan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('tidak_layak');

            $laporanpokja3 = DB::table('laporan_kader_pokja3')
                ->join('users_mobile', 'laporan_kader_pokja3.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kader_pokja3.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kader_pokja3.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_kader_pokja3.status', $status)
                ->get();

            $total10 = LaporanPokja3::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('pangan');
            $total11 = LaporanPokja3::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('sandang');
            $total12 = LaporanPokja3::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('tata_laksana_rumah');

            // Cek data kosong
            $allTablesEmpty = $pangan->isEmpty() && $sandang->isEmpty() && $perumahan->isEmpty() && $laporanpokja3->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search2');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja III')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_tahun_pokja3', compact(
                'pangan',
                'sandang',
                'perumahan',
                'laporanpokja3',
                'total1',
                'total2',
                'total3',
                'total4',
                'total5',
                'total6',
                'total7',
                'total8',
                'total9',
                'total10',
                'total11',
                'total12',
                'total24',
                'total25',
                'total26',
                'total31',
                'formattedDate',
                'ketua',
                'wakil',
                'created_at'
            ));
        } else {
            return redirect()->back();
        }
    }

    public function destroy(string $id_pokja3_bidang1)
    {
        $data = Pangan::find($id_pokja3_bidang1);
        $data->delete();
        return redirect()->route('pangan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
