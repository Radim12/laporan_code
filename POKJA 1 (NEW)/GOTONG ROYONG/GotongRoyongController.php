<?php

namespace App\Http\Controllers\backend;

use Carbon\Carbon;
use App\Models\Ttd;
use App\Models\Ttds;
use App\Models\Penghayatan;
use App\Models\GotongRoyong;
use Illuminate\Http\Request;
use App\Models\LaporanPokja1;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Auth;

class GotongRoyongController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_gotong_royong')
                ->join('users_mobile', 'laporan_gotong_royong.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_gotong_royong.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_gotong_royong.status', 'Proses')
                ->orderBy('laporan_gotong_royong.id_pokja1_bidang2', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah disetujui1
            $data = DB::table('laporan_gotong_royong')
                ->join('users_mobile', 'laporan_gotong_royong.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_gotong_royong.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_gotong_royong.status', 'Disetujui1')
                ->orderBy('laporan_gotong_royong.id_pokja1_bidang2', 'desc')
                ->get();
        }

        return view('backend.gotongroyong', compact('data'));
    }

    public function edit(string $id_pokja1_bidang2)
    {
        $data = GotongRoyong::find($id_pokja1_bidang2);

        // Ambil data user terkait untuk menampilkan info kecamatan/desa
        $user = Pengguna::find($data->id_user);
        $kecamatan = DB::table('subdistrict')->where('id', $user->id_subdistrict)->first();
        $desa = DB::table('village')->where('id', $user->id_village)->first();

        return view('backend.tampil_gotongroyong', compact('data', 'kecamatan', 'desa'));
    }

    public function update(Request $request, string $id_pokja1_bidang2)
    {
        $data = GotongRoyong::find($id_pokja1_bidang2);

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
            'kerja_bakti' => $request->kerja_bakti,
            'rukun_kematian' => $request->rukun_kematian,
            'keagamaan' => $request->keagamaan,
            'jimpitan' => $request->jimpitan,
            'arisan' => $request->arisan,
            'status' => $status,
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        return redirect()->route('gotongroyong.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function filter(Request $request)
    {
        // Tentukan status berdasarkan guard yang aktif
        $status = Auth::guard('web')->check() ? 'Disetujui2' : 'Disetujui1';

        if ($request->has('search')) {
            // Filter berdasarkan bulan
            $gotongroyong = DB::table('laporan_gotong_royong')
                ->join('users_mobile', 'laporan_gotong_royong.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_gotong_royong.*', 'subdistrict.name as nama_kec')
                ->where('laporan_gotong_royong.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_gotong_royong.status', $status)
                ->get();

            $penghayatan = DB::table('laporan_penghayatan_n_pengamalan')
                ->join('users_mobile', 'laporan_penghayatan_n_pengamalan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_penghayatan_n_pengamalan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_penghayatan_n_pengamalan.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_penghayatan_n_pengamalan.status', $status)
                ->get();

            $laporanpokja1 = DB::table('laporan_kader_pokja1')
                ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kader_pokja1.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kader_pokja1.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_kader_pokja1.status', $status)
                ->get();

            // Hitung total untuk Gotong Royong
            $total5 = $gotongroyong->sum('kerja_bakti');
            $total6 = $gotongroyong->sum('rukun_kematian');
            $total7 = $gotongroyong->sum('keagamaan');
            $total8 = $gotongroyong->sum('jimpitan');
            $total9 = $gotongroyong->sum('arisan');

            // Hitung total untuk Penghayatan
            $total12 = $penghayatan->sum('jumlah_kel_simulasi1');
            $total13 = $penghayatan->sum('jumlah_anggota1');
            $total14 = $penghayatan->sum('jumlah_kel_simulasi2');
            $total15 = $penghayatan->sum('jumlah_anggota2');
            $total16 = $penghayatan->sum('jumlah_kel_simulasi3');
            $total17 = $penghayatan->sum('jumlah_anggota3');
            $total18 = $penghayatan->sum('jumlah_kel_simulasi4');
            $total19 = $penghayatan->sum('jumlah_anggota4');

            // Hitung total untuk Kader Pokja 1
            $total = $laporanpokja1->sum('PKBN');
            $total1 = $laporanpokja1->sum('PKDRT');
            $total2 = $laporanpokja1->sum('pola_asuh');

            // Cek data kosong
            $allTablesEmpty = $gotongroyong->isEmpty() && $penghayatan->isEmpty() && $laporanpokja1->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search');
            $carbonDate = Carbon::parse($created_at);
            $created_at = $carbonDate->isoFormat('MMMM YYYY');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja I')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_bulan_pokja1', compact(
                'gotongroyong',
                'penghayatan',
                'laporanpokja1',
                'total5',
                'total6',
                'total7',
                'total8',
                'total9',
                'total12',
                'total13',
                'total14',
                'total15',
                'total16',
                'total17',
                'total18',
                'total19',
                'total',
                'total1',
                'total2',
                'created_at',
                'formattedDate',
                'ketua',
                'wakil'
            ));
        } elseif ($request->has('search2')) {
            // Filter berdasarkan tahun
            $gotongroyong = DB::table('laporan_gotong_royong')
                ->join('users_mobile', 'laporan_gotong_royong.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_gotong_royong.*', 'subdistrict.name as nama_kec')
                ->whereYear('laporan_gotong_royong.created_at', $request->search2)
                ->where('laporan_gotong_royong.status', $status)
                ->get();

            $penghayatan = DB::table('laporan_penghayatan_n_pengamalan')
                ->join('users_mobile', 'laporan_penghayatan_n_pengamalan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_penghayatan_n_pengamalan.*', 'subdistrict.name as nama_kec')
                ->whereYear('laporan_penghayatan_n_pengamalan.created_at', $request->search2)
                ->where('laporan_penghayatan_n_pengamalan.status', $status)
                ->get();

            $laporanpokja1 = DB::table('laporan_kader_pokja1')
                ->join('users_mobile', 'laporan_kader_pokja1.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kader_pokja1.*', 'subdistrict.name as nama_kec')
                ->whereYear('laporan_kader_pokja1.created_at', $request->search2)
                ->where('laporan_kader_pokja1.status', $status)
                ->get();

            // Hitung total untuk Gotong Royong
            $total5 = $gotongroyong->sum('kerja_bakti');
            $total6 = $gotongroyong->sum('rukun_kematian');
            $total7 = $gotongroyong->sum('keagamaan');
            $total8 = $gotongroyong->sum('jimpitan');
            $total9 = $gotongroyong->sum('arisan');

            // Hitung total untuk Penghayatan
            $total12 = $penghayatan->sum('jumlah_kel_simulasi1');
            $total13 = $penghayatan->sum('jumlah_anggota1');
            $total14 = $penghayatan->sum('jumlah_kel_simulasi2');
            $total15 = $penghayatan->sum('jumlah_anggota2');
            $total16 = $penghayatan->sum('jumlah_kel_simulasi3');
            $total17 = $penghayatan->sum('jumlah_anggota3');
            $total18 = $penghayatan->sum('jumlah_kel_simulasi4');
            $total19 = $penghayatan->sum('jumlah_anggota4');

            // Hitung total untuk Kader Pokja 1
            $total = $laporanpokja1->sum('PKBN');
            $total1 = $laporanpokja1->sum('PKDRT');
            $total2 = $laporanpokja1->sum('pola_asuh');

            // Cek data kosong
            $allTablesEmpty = $gotongroyong->isEmpty() && $penghayatan->isEmpty() && $laporanpokja1->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search2');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja I')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_tahun_pokja1', compact(
                'gotongroyong',
                'penghayatan',
                'laporanpokja1',
                'total5',
                'total6',
                'total7',
                'total8',
                'total9',
                'total12',
                'total13',
                'total14',
                'total15',
                'total16',
                'total17',
                'total18',
                'total19',
                'total',
                'total1',
                'total2',
                'created_at',
                'formattedDate',
                'ketua',
                'wakil'
            ));
        } else {
            return redirect()->back();
        }
    }

    public function destroy(string $id_pokja1_bidang2)
    {
        $data = GotongRoyong::find($id_pokja1_bidang2);
        $data->delete();
        return redirect()->route('gotongroyong.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
