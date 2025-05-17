<?php

// namespace App\Http\Controllers\backend;

// use Carbon\Carbon;
// use App\Models\Kesehatan;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\Models\KelestarianLingkunganHidup;
// use App\Models\PerencanaanSehat;
// use Illuminate\Support\Facades\DB;
// use App\Models\Ttd;
// use App\Models\Ttds;

// class KesehatanController extends Controller
// {
//     public function index(Request $request) {
//         $data = DB::table('laporan_bidang_kesehatan')
//         ->join('penggunas', 'laporan_bidang_kesehatan.id_user', '=', 'penggunas.id')
//         ->select('laporan_bidang_kesehatan.*', 'penggunas.nama_kec')
//         ->where('laporan_bidang_kesehatan.status', 'proses')
//         ->orderBy('id_laporan_sehat', 'desc')
//         ->get();
//         return view('backend.kesehatan', compact('data'));
//     }

//     public function edit(string $id_laporan_sehat)
//     {
//         $data = Kesehatan::find($id_laporan_sehat);
//         return view('backend.tampil_kesehatan', compact('data'));
//     }
//     public function update(Request $request, string $id_laporan_sehat)
//     {
//         $data = Kesehatan::find($id_laporan_sehat);
//             $data->update([
//                 'jumlah_posyandu' => $request->jumlah_posyandu,
//                 'jumlah_posyandu_iterasi' => $request->jumlah_posyandu_iterasi,
//                 'jumlah_klp' => $request->jumlah_klp,
//                 'jumlah_anggota' => $request->jumlah_anggota,
//                 'jumlah_kartu_gratis' => $request->jumlah_kartu_gratis,
//                 'id_user' => $request->id_user,
//                 'status' => $request->status,
//                 'catatan' => $request->catatan,
//                 'created_at' => $request->created_at,
//             ]);
//         return redirect()->route('kesehatan.index')->with(['success' => 'Berhasil Mengubah Status']);
//     }
//     // public function filter(Request $request)
//     // {
//     //     $bulan = $request->bulan;
//     //     $tahun = $request->tahun;
//     //     $kesehatan = Kesehatan::where('created_at', $bulan)->get(); // Filter data berdasarkan bulan
//     //     return view('backend.cetak_bulan_kesehatan', compact('kesehatan'));
//     // }

//     public function show(Request $request)
//     {
//         if ($request->has('search')) {
//             $kesehatan = DB::table('laporan_bidang_kesehatan')
//             ->join('penggunas', 'laporan_bidang_kesehatan.id_user', '=', 'penggunas.id')
//             ->select('laporan_bidang_kesehatan.*', 'penggunas.nama_kec')
//             ->where('laporan_bidang_kesehatan.created_at', 'LIKE', '%' . $request->search . '%')
//             ->where('laporan_bidang_kesehatan.status', 'disetujui')
//             ->get();

//             $total = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('jumlah_posyandu');
//             $total1 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('jumlah_posyandu_iterasi');
//             $total2 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('jumlah_klp');
//             $total3 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('jumlah_anggota');
//             $total4 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('jumlah_kartu_gratis');

//              $kelestarian = DB::table('laporan_kelestarian_lingkungan_hidup')
//             ->join('penggunas', 'laporan_kelestarian_lingkungan_hidup.id_user', '=', 'penggunas.id')
//             ->select('laporan_kelestarian_lingkungan_hidup.*', 'penggunas.nama_kec')
//             ->where('laporan_kelestarian_lingkungan_hidup.created_at', 'LIKE', '%' . $request->search . '%')
//             ->where('laporan_kelestarian_lingkungan_hidup.status', 'disetujui')
//             ->get();
//             $total5 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('jamban');
//             $total6 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('spal');
//             $total7 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('tps');
//             $total8 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('mck');
//             $total9 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('pdam');
//             $total10 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('sumur');
//             $total11 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('dll');

//             $perencanaan = DB::table('laporan_perencanaan_sehat')
//             ->join('penggunas', 'laporan_perencanaan_sehat.id_user', '=', 'penggunas.id')
//             ->select('laporan_perencanaan_sehat.*', 'penggunas.nama_kec')
//             ->where('laporan_perencanaan_sehat.created_at', 'LIKE', '%' . $request->search . '%')
//             ->where('laporan_perencanaan_sehat.status', 'disetujui')
//             ->get();
//             $total12 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('J_Psubur');
//             $total13 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('J_Wsubur');
//             $total14 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('Kb_p');
//             $total15 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('Kb_w');
//             $total16 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')->where('status', 'disetujui')->sum('Kk_tbg');

//             $currentDate = Carbon::now();
//             $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
//             $created_at = $request->input('search');
//             $carbonDate = Carbon::parse($created_at);
//             $created_at = $carbonDate->isoFormat('MMMM YYYY'); 
//             $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja IV')->get();
//             $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

//             return view('backend.cetak_bulan_pokja4', compact('kesehatan', 'total', 'total1', 'total2', 'total3', 'total4', 'created_at', 'kelestarian',
//             'total5', 'total6', 'total7', 'total8', 'total9', 'total10', 'total11', 'perencanaan', 'total12', 'total13', 'total14', 'total15', 'total16', 'formattedDate', 'ketua', 'wakil'));
//         }elseif ($request->has('search2')){
//             $kesehatan = DB::table('laporan_bidang_kesehatan')
//             ->join('penggunas', 'laporan_bidang_kesehatan.id_user', '=', 'penggunas.id')
//             ->select('laporan_bidang_kesehatan.*', 'penggunas.nama_kec')
//             ->where('laporan_bidang_kesehatan.created_at', 'LIKE', '%' . $request->search2 . '%')
//             ->where('laporan_bidang_kesehatan.status', 'disetujui')
//             ->get();
//             $total = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('jumlah_posyandu');
//             $total1 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('jumlah_posyandu_iterasi');
//             $total2 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('jumlah_klp');
//             $total3 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('jumlah_anggota');
//             $total4 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('jumlah_kartu_gratis');

//             $kelestarian = DB::table('laporan_kelestarian_lingkungan_hidup')
//             ->join('penggunas', 'laporan_kelestarian_lingkungan_hidup.id_user', '=', 'penggunas.id')
//             ->select('laporan_kelestarian_lingkungan_hidup.*', 'penggunas.nama_kec')
//             ->where('laporan_kelestarian_lingkungan_hidup.created_at', 'LIKE', '%' . $request->search2 . '%')
//             ->where('laporan_kelestarian_lingkungan_hidup.status', 'disetujui')
//             ->get();
//             $total5 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('jamban');
//             $total6 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('spal');
//             $total7 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('tps');
//             $total8 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('mck');
//             $total9 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('pdam');
//             $total10 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('sumur');
//             $total11 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('dll');

//             $perencanaan = DB::table('laporan_perencanaan_sehat')
//             ->join('penggunas', 'laporan_perencanaan_sehat.id_user', '=', 'penggunas.id')
//             ->select('laporan_perencanaan_sehat.*', 'penggunas.nama_kec')
//             ->where('laporan_perencanaan_sehat.created_at', 'LIKE', '%' . $request->search2 . '%')
//             ->where('laporan_perencanaan_sehat.status', 'disetujui')
//             ->get();
//             $total12 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('J_Psubur');
//             $total13 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('J_Wsubur');
//             $total14 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('Kb_p');
//             $total15 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('Kb_w');
//             $total16 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')->where('status', 'disetujui')->sum('Kk_tbg');

//             $currentDate = Carbon::now();
//             $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
//             $created_at = $request->input('search2');
//             $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja IV')->get();
//             $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

//             return view('backend.cetak_tahun_pokja4', compact('kesehatan', 'total', 'total1', 'total2', 'total3', 'total4', 'created_at', 'kelestarian',
//             'total5', 'total6', 'total7', 'total8', 'total9', 'total10', 'total11', 'perencanaan', 'total12', 'total13', 'total14', 'total15', 'total16', 'formattedDate', 'ketua', 'wakil'));
//         }else{

//         }

//     }
//     public function destroy(string $id_laporan_sehat)
//     {

//         $data = Kesehatan::find($id_laporan_sehat);

//         $data->delete();

//         return redirect()->route('kesehatan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
//     }
// }





//kode asli
namespace App\Http\Controllers\backend;

use Carbon\Carbon;
use App\Models\Kesehatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KelestarianLingkunganHidup;
use App\Models\Pengguna;
use App\Models\PerencanaanSehat;
use Illuminate\Support\Facades\DB;
use App\Models\Ttd;
use App\Models\Ttds;
use Illuminate\Support\Facades\Auth;

class KesehatanController extends Controller
{
    public function index()
    {
        // Cek guard yang aktif
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (mobile) - role kecamatan, tampilkan data proses
            $user = Auth::guard('pengguna')->user();

            $data = DB::table('laporan_bidang_kesehatan')
                ->join('users_mobile', 'laporan_bidang_kesehatan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_bidang_kesehatan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('users_mobile.id_subdistrict', $user->id_subdistrict)
                ->where('users_mobile.id_role', 1)
                ->where('laporan_bidang_kesehatan.status', 'Proses')
                ->orderBy('laporan_bidang_kesehatan.id_pokja4_bidang1', 'desc')
                ->get();
        } else {
            // Jika guard web - tampilkan data yang sudah Disetujui1
            $data = DB::table('laporan_bidang_kesehatan')
                ->join('users_mobile', 'laporan_bidang_kesehatan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->join('village', 'users_mobile.id_village', '=', 'village.id')
                ->select(
                    'laporan_bidang_kesehatan.*',
                    'subdistrict.name as nama_kec',
                    'village.name as nama_desa'
                )
                ->where('laporan_bidang_kesehatan.status', 'Disetujui1')
                ->orderBy('laporan_bidang_kesehatan.id_pokja4_bidang1', 'desc')
                ->get();
        }

        return view('backend.kesehatan', compact('data'));
    }

    public function edit(string $id_pokja4_bidang1)
    {
        $data = Kesehatan::find($id_pokja4_bidang1);

        // Ambil data user terkait untuk menampilkan info kecamatan/desa
        $user = Pengguna::find($data->id_user);
        $kecamatan = DB::table('subdistrict')->where('id', $user->id_subdistrict)->first();
        $desa = DB::table('village')->where('id', $user->id_village)->first();

        return view('backend.tampil_kesehatan', compact('data', 'kecamatan', 'desa'));
    }

    public function update(Request $request, string $id_pokja4_bidang1)
    {
        $data = Kesehatan::find($id_pokja4_bidang1);

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
            'jumlah_posyandu' => $request->jumlah_posyandu,
            'jumlah_posyandu_iterasi' => $request->jumlah_posyandu_iterasi,
            'jumlah_klp' => $request->jumlah_klp,
            'jumlah_anggota' => $request->jumlah_anggota,
            'jumlah_kartu_gratis' => $request->jumlah_kartu_gratis,
            'status' => $status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('kesehatan.index')->with(['success' => 'Berhasil Mengubah Status']);
    }

    public function show(Request $request)
    {
        // Tentukan status berdasarkan guard yang aktif
        $status = Auth::guard('web')->check() ? 'Disetujui2' : 'Disetujui1';



        if ($request->has('search')) {
            // Query untuk laporan bidang kesehatan
            $kesehatan = DB::table('laporan_bidang_kesehatan')
                ->join('users_mobile', 'laporan_bidang_kesehatan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_bidang_kesehatan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_bidang_kesehatan.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_bidang_kesehatan.status', $status)
                ->get();

            $total = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jumlah_posyandu');
            $total1 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jumlah_posyandu_iterasi');
            $total2 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jumlah_klp');
            $total3 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jumlah_anggota');
            $total4 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jumlah_kartu_gratis');

            // Query untuk laporan kelestarian lingkungan hidup
            $kelestarian = DB::table('laporan_kelestarian_lingkungan_hidup')
                ->join('users_mobile', 'laporan_kelestarian_lingkungan_hidup.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kelestarian_lingkungan_hidup.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kelestarian_lingkungan_hidup.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_kelestarian_lingkungan_hidup.status', $status)
                ->get();

            $total5 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('jamban');
            $total6 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('spal');
            $total7 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('tps');
            $total8 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('mck');
            $total9 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('pdam');
            $total10 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('sumur');
            $total11 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('dll');

            // Query untuk laporan perencanaan sehat
            $perencanaan = DB::table('laporan_perencanaan_sehat')
                ->join('users_mobile', 'laporan_perencanaan_sehat.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_perencanaan_sehat.*', 'subdistrict.name as nama_kec')
                ->where('laporan_perencanaan_sehat.created_at', 'LIKE', '%' . $request->search . '%')
                ->where('laporan_perencanaan_sehat.status', $status)
                ->get();

            $total12 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('J_Psubur');
            $total13 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('J_Wsubur');
            $total14 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('Kb_p');
            $total15 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('Kb_w');
            $total16 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search . '%')
                ->where('status', $status)
                ->sum('Kk_tbg');

            //cek data kosong
            $allTablesEmpty = $kesehatan->isEmpty() && $kelestarian->isEmpty() && $perencanaan->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search');
            $carbonDate = Carbon::parse($created_at);
            $created_at = $carbonDate->isoFormat('MMMM YYYY');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja IV')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();

            return view('backend.cetak_bulan_pokja4', compact(
                'kesehatan',
                'total',
                'total1',
                'total2',
                'total3',
                'total4',
                'created_at',
                'kelestarian',
                'total5',
                'total6',
                'total7',
                'total8',
                'total9',
                'total10',
                'total11',
                'perencanaan',
                'total12',
                'total13',
                'total14',
                'total15',
                'total16',
                'formattedDate',
                'ketua',
                'wakil'
            ));
        } elseif ($request->has('search2')) {
            // Logika yang sama untuk search2
            $kesehatan = DB::table('laporan_bidang_kesehatan')
                ->join('users_mobile', 'laporan_bidang_kesehatan.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_bidang_kesehatan.*', 'subdistrict.name as nama_kec')
                ->where('laporan_bidang_kesehatan.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_bidang_kesehatan.status', $status)
                ->get();


            $total = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jumlah_posyandu');

            $total1 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jumlah_posyandu_iterasi');
            $total2 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jumlah_klp');
            $total3 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jumlah_anggota');
            $total4 = Kesehatan::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jumlah_kartu_gratis');

            // Query untuk laporan kelestarian lingkungan hidup
            $kelestarian = DB::table('laporan_kelestarian_lingkungan_hidup')
                ->join('users_mobile', 'laporan_kelestarian_lingkungan_hidup.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_kelestarian_lingkungan_hidup.*', 'subdistrict.name as nama_kec')
                ->where('laporan_kelestarian_lingkungan_hidup.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_kelestarian_lingkungan_hidup.status', $status)
                ->get();

            $total5 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('jamban');
            $total6 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('spal');
            $total7 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('tps');
            $total8 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('mck');
            $total9 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('pdam');
            $total10 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('sumur');
            $total11 = KelestarianLingkunganHidup::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('dll');

            // Query untuk laporan perencanaan sehat
            $perencanaan = DB::table('laporan_perencanaan_sehat')
                ->join('users_mobile', 'laporan_perencanaan_sehat.id_user', '=', 'users_mobile.id')
                ->join('subdistrict', 'users_mobile.id_subdistrict', '=', 'subdistrict.id')
                ->select('laporan_perencanaan_sehat.*', 'subdistrict.name as nama_kec')
                ->where('laporan_perencanaan_sehat.created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('laporan_perencanaan_sehat.status', $status)
                ->get();

            $total12 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('J_Psubur');
            $total13 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('J_Wsubur');
            $total14 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('Kb_p');
            $total15 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('Kb_w');
            $total16 = PerencanaanSehat::where('created_at', 'LIKE', '%' . $request->search2 . '%')
                ->where('status', $status)
                ->sum('Kk_tbg');
            // ... (lanjutkan dengan query lainnya seperti di atas)

            //cetak data kosong
            $allTablesEmpty = $kesehatan->isEmpty() && $kelestarian->isEmpty() && $perencanaan->isEmpty();

            if ($allTablesEmpty) {
                return back()->with('error', 'Tidak ada data laporan untuk periode tersebut');
            }

            $currentDate = Carbon::now();
            $formattedDate = $currentDate->isoFormat('dddd, D MMMM YYYY');
            $created_at = $request->input('search2');
            $ketua = Ttd::where('jabatan', 'Ketua')->where('pokja', 'Kelompok Kerja IV')->get();
            $wakil = Ttd::where('jabatan', 'Ketua')->where('id_ttds', '12')->get();
            return view('backend.cetak_tahun_pokja4', compact(
                'kesehatan',
                'total',
                'total1',
                'total2',
                'total3',
                'total4',
                'created_at',
                'kelestarian',
                'total5',
                'total6',
                'total7',
                'total8',
                'total9',
                'total10',
                'total11',
                'perencanaan',
                'total12',
                'total13',
                'total14',
                'total15',
                'total16',
                'formattedDate',
                'ketua',
                'wakil'
            ));
        } else {
        }
    }


    public function destroy(string $id_pokja4_bidang1)
    {
        $data = Kesehatan::find($id_pokja4_bidang1);
        $data->delete();
        return redirect()->route('kesehatan.index')->with(['success' => 'Berhasil Menghapus Laporan']);
    }
}
