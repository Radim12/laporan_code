<?php

namespace App\Http\Controllers\backend;

use Carbon\Carbon;
use App\Models\Ttd;
use App\Models\Ttds;
use App\Models\Pendidikan;
use App\Models\Pengembangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Pengguna;

class PendidikanController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_pendidikan_n_keterampilan')
                ->join('users_mobile', 'laporan_pendidikan_n_keterampilan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_pendidikan_n_keterampilan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_pendidikan_n_keterampilan.status', 'Proses')
                ->orderBy('laporan_pendidikan_n_keterampilan.id_pokja2_bidang1', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_pendidikan_n_keterampilan')
                ->join('users_mobile', 'laporan_pendidikan_n_keterampilan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_pendidikan_n_keterampilan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_pendidikan_n_keterampilan.status', 'Disetujui1')
                ->orderBy('laporan_pendidikan_n_keterampilan.id_pokja2_bidang1', 'desc')
                ->get();
        }

        return view('backend.pendidikan', compact('data'));
    }

    public function edit(string $id_pokja2_bidang1)
    {
        $data = Pendidikan::find($id_pokja2_bidang1);

        // Ambil data user terkait untuk menampilkan info kecamatan/desa
        $user = Pengguna::find($data->id_user);
        $kecamatan = DB::table('subdistrict')->where('id', $user->id_subdistrict)->first();
        $desa = DB::table('village')->where('id', $user->id_village)->first();

        return view('backend.tampil_pendidikan', compact('data', 'kecamatan', 'desa'));
    }

    public function update(Request $request, string $id_pokja2_bidang1)
    {
        $data = Pendidikan::find($id_pokja2_bidang1);

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
            'warga_buta' => $request->warga_buta,
            'kel_belajarA' => $request->kel_belajarA,
            'warga_belajarA' => $request->warga_belajarA,
            'kel_belajarB' => $request->kel_belajarB,
            'warga_belajarB' => $request->warga_belajarB,
            'kel_belajarC' => $request->kel_belajarC,
            'warga_belajarC' => $request->warga_belajarC,
            'kel_belajarKF' => $request->kel_belajarKF,
            'warga_belajarKF' => $request->warga_belajarKF,
            'paud' => $request->paud,
            'taman_bacaan' => $request->taman_bacaan,
            'jumlah_klp' => $request->jumlah_klp,
            'jumlah_ibu_peserta' => $request->jumlah_ibu_peserta,
            'jumlah_ape' => $request->jumlah_ape,
            'jumlah_kel_simulasi' => $request->jumlah_kel_simulasi,
            'KF' => $request->KF,
            'paud_tutor' => $request->paud_tutor,
            'BKB' => $request->BKB,
            'koperasi' => $request->koperasi,
            'ketrampilan' => $request->ketrampilan,
            'LP3PKK' => $request->LP3PKK,
            'TP3PKK' => $request->TP3PKK,
            'damas_pkk' => $request->damas_pkk,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pendidikan.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function filter(Request $request)
    {
        // Tentukan status berdasarkan guard yang aktif
        $status = Auth::guard('web')->check() ? 'Disetujui2' : 'Disetujui1';

        if ($request->has('search')) {
            // Query untuk laporan pendidikan dan keterampilan
            $pendidikan = DB::table('laporan_pendidikan_n_keterampilan')
                ->join('users_mobile', 'laporan_pendidikan_n_keterampilan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pendidikan_n_keterampilan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pendidikan_n_keterampilan.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_pendidikan_n_keterampilan.status', $status)
                ->get();

            // Query untuk laporan pengembangan kehidupan
            $pengembangan = DB::table('laporan_pengembangan_kehidupan')
                ->join('users_mobile', 'laporan_pengembangan_kehidupan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pengembangan_kehidupan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pengembangan_kehidupan.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_pengembangan_kehidupan.status', $status)
                ->get();

            // Hitung total untuk pendidikan
            $total1 = $pendidikan->sum('warga_buta');
            $total2 = $pendidikan->sum('kel_belajarA');
            $total3 = $pendidikan->sum('warga_belajarA');
            $total4 = $pendidikan->sum('kel_belajarB');
            $total5 = $pendidikan->sum('warga_belajarB');
            $total6 = $pendidikan->sum('kel_belajarC');
            $total7 = $pendidikan->sum('warga_belajarC');
            $total8 = $pendidikan->sum('kel_belajarKF');
            $total9 = $pendidikan->sum('warga_belajarKF');
            $total10 = $pendidikan->sum('paud');
            $total11 = $pendidikan->sum('taman_bacaan');
            $total12 = $pendidikan->sum('jumlah_klp');
            $total13 = $pendidikan->sum('jumlah_ibu_peserta');
            $total14 = $pendidikan->sum('jumlah_ape');
            $total15 = $pendidikan->sum('jumlah_kel_simulasi');
            $total16 = $pendidikan->sum('KF');
            $total17 = $pendidikan->sum('paud_tutor');
            $total18 = $pendidikan->sum('BKB');
            $total19 = $pendidikan->sum('koperasi');
            $total20 = $pendidikan->sum('ketrampilan');
            $total21 = $pendidikan->sum('LP3PKK');
            $total22 = $pendidikan->sum('TP3PKK');
            $total23 = $pendidikan->sum('damas_pkk');

            // Hitung total untuk pengembangan
            $total24 = $pengembangan->sum('jumlah_kelompok_pemula');
            $total25 = $pengembangan->sum('jumlah_peserta_pemula');
            $total26 = $pengembangan->sum('jumlah_kelompok_madya');
            $total27 = $pengembangan->sum('jumlah_peserta_madya');
            $total28 = $pengembangan->sum('jumlah_kelompok_utama');
            $total29 = $pengembangan->sum('jumlah_peserta_utama');
            $total30 = $pengembangan->sum('jumlah_kelompok_mandiri');
            $total31 = $pengembangan->sum('jumlah_peserta_mandiri');
            $total32 = $pengembangan->sum('jumlah_kelompok_hukum');
            $total33 = $pengembangan->sum('jumlah_peserta_hukum');

            // Cek data kosong
            $allTablesEmpty = $pendidikan->isEmpty() && $pengembangan->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search');
            $carbonDate = Carbon::parse($created_at);
            $created_at = $carbonDate->isoFormat('MMMM YYYY');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja II')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_bulan_pokja2', compact(
                'pendidikan',
                'pengembangan',
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
                'total13',
                'total14',
                'total15',
                'total16',
                'total17',
                'total18',
                'total19',
                'total20',
                'total21',
                'total22',
                'total23',
                'total24',
                'total25',
                'total26',
                'total27',
                'total28',
                'total29',
                'total30',
                'total31',
                'total32',
                'total33',
                'formattedDate',
                'ketua',
                'wakil',
                'created_at'
            ));
        } elseif ($request->has('search2')) {
            // Query untuk laporan pendidikan dan keterampilan (tahunan)
            $pendidikan = DB::table('laporan_pendidikan_n_keterampilan')
                ->join('users_mobile', 'laporan_pendidikan_n_keterampilan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pendidikan_n_keterampilan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pendidikan_n_keterampilan.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_pendidikan_n_keterampilan.status', $status)
                ->get();

            // Query untuk laporan pengembangan kehidupan (tahunan)
            $pengembangan = DB::table('laporan_pengembangan_kehidupan')
                ->join('users_mobile', 'laporan_pengembangan_kehidupan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_pengembangan_kehidupan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_pengembangan_kehidupan.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_pengembangan_kehidupan.status', $status)
                ->get();

            // Hitung total untuk pendidikan
            $total1 = $pendidikan->sum('warga_buta');
            $total2 = $pendidikan->sum('kel_belajarA');
            $total3 = $pendidikan->sum('warga_belajarA');
            $total4 = $pendidikan->sum('kel_belajarB');
            $total5 = $pendidikan->sum('warga_belajarB');
            $total6 = $pendidikan->sum('kel_belajarC');
            $total7 = $pendidikan->sum('warga_belajarC');
            $total8 = $pendidikan->sum('kel_belajarKF');
            $total9 = $pendidikan->sum('warga_belajarKF');
            $total10 = $pendidikan->sum('paud');
            $total11 = $pendidikan->sum('taman_bacaan');
            $total12 = $pendidikan->sum('jumlah_klp');
            $total13 = $pendidikan->sum('jumlah_ibu_peserta');
            $total14 = $pendidikan->sum('jumlah_ape');
            $total15 = $pendidikan->sum('jumlah_kel_simulasi');
            $total16 = $pendidikan->sum('KF');
            $total17 = $pendidikan->sum('paud_tutor');
            $total18 = $pendidikan->sum('BKB');
            $total19 = $pendidikan->sum('koperasi');
            $total20 = $pendidikan->sum('ketrampilan');
            $total21 = $pendidikan->sum('LP3PKK');
            $total22 = $pendidikan->sum('TP3PKK');
            $total23 = $pendidikan->sum('damas_pkk');

            // Hitung total untuk pengembangan
            $total24 = $pengembangan->sum('jumlah_kelompok_pemula');
            $total25 = $pengembangan->sum('jumlah_peserta_pemula');
            $total26 = $pengembangan->sum('jumlah_kelompok_madya');
            $total27 = $pengembangan->sum('jumlah_peserta_madya');
            $total28 = $pengembangan->sum('jumlah_kelompok_utama');
            $total29 = $pengembangan->sum('jumlah_peserta_utama');
            $total30 = $pengembangan->sum('jumlah_kelompok_mandiri');
            $total31 = $pengembangan->sum('jumlah_peserta_mandiri');
            $total32 = $pengembangan->sum('jumlah_kelompok_hukum');
            $total33 = $pengembangan->sum('jumlah_peserta_hukum');

            // Cek data kosong
            $allTablesEmpty = $pendidikan->isEmpty() && $pengembangan->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search2');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja II')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_tahun_pokja2', compact(
                'pendidikan',
                'pengembangan',
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
                'total13',
                'total14',
                'total15',
                'total16',
                'total17',
                'total18',
                'total19',
                'total20',
                'total21',
                'total22',
                'total23',
                'total24',
                'total25',
                'total26',
                'total27',
                'total28',
                'total29',
                'total30',
                'total31',
                'total32',
                'total33',
                'formattedDate',
                'ketua',
                'wakil',
                'created_at'
            ));
        }

        return redirect()->back();
    }

    public function destroy(string $id_pokja2_bidang1)
    {
        $data = Pendidikan::find($id_pokja2_bidang1);
        $data->delete();
        return redirect()->route('pendidikan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
