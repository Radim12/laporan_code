<?php

namespace App\Http\Controllers\backend;

use App\Models\BidangUmum;
use App\Models\Ttd;
use App\Models\Ttds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BidangUmumController extends Controller
{
    public function index()
    {
        // Cek guard yang digunakan
        if (Auth::guard('web')->check()) {
            // Jika guard web (admin), tampilkan semua data dengan status 'Disetujui1' atau 'Disetujui2'
            $data = DB::table('laporan_umum')
                ->join('users_mobile', 'laporan_umum.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_umum.*', 'subdistrict.name as nama_kec')
                ->whereIn('laporan_umum.status', ['Disetujui1'])
                ->orderBy('id_laporan_umum', 'desc')
                ->get();
        } else {
            // Jika guard pengguna
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan (role 2)
                // Tampilkan data desa (role 1) di kecamatan tersebut dengan status 'proses'
                $data = DB::table('laporan_umum')
                    ->join('users_mobile', 'laporan_umum.id_user', '=', 'users_mobile.id')
                    ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                    ->select('laporan_umum.*', 'subdistrict.name as nama_kec')
                    ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1) // Hanya desa
                    ->where('laporan_umum.status', ['proses'])
                    ->orderBy('id_laporan_umum', 'desc')
                    ->get();
            }
        }

        return view('backend.bidangumum', compact('data'));
    }

    public function edit(string $id_laporan_umum)
    {
        $data = BidangUmum::find($id_laporan_umum);
        return view('backend.tampil_bidangumum', compact('data'));
    }
    public function update(Request $request, string $id_laporan_umum)
    {
        $data = BidangUmum::find($id_laporan_umum);
        $data->update([
            'dusun_lingkungan' => $request->dusun_lingkungan,
            'PKK_RW' => $request->PKK_RW,
            'desa_wisma' => $request->desa_wisma,
            'KRT' => $request->KRT,
            'KK' => $request->KK,
            'jiwa_laki' => $request->jiwa_laki,
            'jiwa_perempuan' => $request->jiwa_perempuan,
            'anggota_laki' => $request->anggota_laki,
            'anggota_perempuan' => $request->anggota_perempuan,
            'umum_laki' => $request->umum_laki,
            'umum_perempuan' => $request->umum_perempuan,
            'khusus_laki' => $request->khusus_laki,
            'khusus_perempuan' => $request->khusus_perempuan,
            'honorer_laki' => $request->honorer_laki,
            'honorer_perempuan' => $request->honorer_perempuan,
            'bantuan_laki' => $request->bantuan_laki,
            'bantuan_perempuan' => $request->bantuan_perempuan,
            'id_user' => $request->id_user,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'created_at' => $request->created_at,
        ]);
        return redirect()->route('bidangumum.index')->with(['success' => 'Berhasil Mengubah Status']);
    }
    public function filter(Request $request)
    {
        // Tentukan status berdasarkan guard yang aktif
        $status = Auth::guard('web')->check() ? 'Disetujui2' : 'Disetujui1';

        $baseQuery = DB::table('laporan_umum')
            ->join('users_mobile', 'laporan_umum.id_user', '=', 'users_mobile.id')
            ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
            ->select('laporan_umum.*', 'subdistrict.name as nama_kec')
            ->where('laporan_umum.status', $status);

        // Jika guard pengguna (mobile) - tambahkan filter berdasarkan role
        if (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) { // Kecamatan
                $baseQuery->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                    ->where('users_mobile.id_role', 1); // Hanya desa
            }
        }

        if ($request->has('search')) {
            $bidangumum = $baseQuery->where('laporan_umum.created_at', 'LIKE', '%' . $request->search . '%')
                ->get();

            // Cek data kosong
            if ($bidangumum->isEmpty()) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $total1 = $bidangumum->sum('dusun_lingkungan');
            $total2 = $bidangumum->sum('PKK_RW');
            $total3 = $bidangumum->sum('desa_wisma');
            $total4 = $bidangumum->sum('KRT');
            $total5 = $bidangumum->sum('KK');
            $total6 = $bidangumum->sum('jiwa_laki');
            $total7 = $bidangumum->sum('jiwa_perempuan');
            $total8 = $bidangumum->sum('anggota_laki');
            $total9 = $bidangumum->sum('anggota_perempuan');
            $total10 = $bidangumum->sum('umum_laki');
            $total11 = $bidangumum->sum('umum_perempuan');
            $total12 = $bidangumum->sum('khusus_laki');
            $total13 = $bidangumum->sum('khusus_perempuan');
            $total14 = $bidangumum->sum('honorer_laki');
            $total15 = $bidangumum->sum('honorer_perempuan');
            $total16 = $bidangumum->sum('bantuan_laki');
            $total17 = $bidangumum->sum('bantuan_perempuan');

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $tanggal = $request->input('search');
            $carbonDate = Carbon::parse($tanggal);
            $tanggal = $carbonDate->isoFormat('MMMM YYYY');
            $ketua = Ttd::where('jabatan', 'Sekretaris')->where('pokja', 'Bidang Umum')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_bulan_bidangumum', compact(
                'bidangumum',
                'total1',
                'total2',
                'total3',
                'total4',
                'tanggal',
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
                'formattedDate',
                'ketua',
                'wakil'
            ));
        } elseif ($request->has('search2')) {
            $bidangumum = $baseQuery->where('laporan_umum.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->get();

            // Cek data kosong
            if ($bidangumum->isEmpty()) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $total1 = $bidangumum->sum('dusun_lingkungan');
            $total2 = $bidangumum->sum('PKK_RW');
            $total3 = $bidangumum->sum('desa_wisma');
            $total4 = $bidangumum->sum('KRT');
            $total5 = $bidangumum->sum('KK');
            $total6 = $bidangumum->sum('jiwa_laki');
            $total7 = $bidangumum->sum('jiwa_perempuan');
            $total8 = $bidangumum->sum('anggota_laki');
            $total9 = $bidangumum->sum('anggota_perempuan');
            $total10 = $bidangumum->sum('umum_laki');
            $total11 = $bidangumum->sum('umum_perempuan');
            $total12 = $bidangumum->sum('khusus_laki');
            $total13 = $bidangumum->sum('khusus_perempuan');
            $total14 = $bidangumum->sum('honorer_laki');
            $total15 = $bidangumum->sum('honorer_perempuan');
            $total16 = $bidangumum->sum('bantuan_laki');
            $total17 = $bidangumum->sum('bantuan_perempuan');

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $tanggal = $request->input('search2');
            $ketua = Ttd::where('jabatan', 'Sekretaris')->where('pokja', 'Bidang Umum')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_tahun_bidangumum', compact(
                'bidangumum',
                'total1',
                'total2',
                'total3',
                'total4',
                'tanggal',
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
                'formattedDate',
                'ketua',
                'wakil'
            ));
        }
    }
    public function destroy(string $id_laporan_umum)
    {
        $data = BidangUmum::find($id_laporan_umum);
        $data->delete();
        return redirect()->route('bidangumum.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
