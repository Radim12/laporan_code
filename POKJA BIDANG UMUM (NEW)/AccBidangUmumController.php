<?php

// namespace App\Http\Controllers\backend;

// use Illuminate\Http\Request;;
// use App\Models\BidangUmum;
// use App\Http\Controllers\Controller;

// class AccBidangUmumController extends Controller
// {
//     public function index(){

//         $got1 = BidangUmum::where('status', 'proses')->count();
//         $got2 = BidangUmum::where('status', 'disetujui')->count();

//         return view('backend.accbidangumum', compact('got1', 'got2'));
//     }    

// }


namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\BidangUmum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;
use App\Models\Subdistrict;
use App\Models\Village;

class AccBidangUmumController extends Controller
{
    public function index()
    {
        // Cek guard yang login
        if (Auth::guard('pengguna')->check()) {
            // Jika guard pengguna (kecamatan)
            $user = Auth::guard('pengguna')->user();

            if ($user->id_role == 2) {
                // Jika role kecamatan (2), ambil data desa (role 1) di kecamatan yang sama
                $desaIds = Pengguna::where('id_subdistrict', $user->id_subdistrict)
                    ->where('id_role', 1)
                    ->pluck('id');

                $got1 = BidangUmum::whereIn('id_user', $desaIds)
                    ->where('status', 'proses')
                    ->count();

                $got2 = BidangUmum::whereIn('id_user', $desaIds)
                    ->where('status', 'Disetujui1')
                    ->count();
            }
        } else {
            // Jika guard web, ambil semua data
            $got1 = BidangUmum::where('status', 'Disetujui1')->count();
            $got2 = BidangUmum::where('status', 'Disetujui2')->count();
        }

        return view('backend.accbidangumum', compact('got1', 'got2'));
    }
}
