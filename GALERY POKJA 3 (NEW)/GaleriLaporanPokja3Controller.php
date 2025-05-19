<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Galeri;
use App\Http\Controllers\Controller;

class GaleriLaporanPokja3Controller extends Controller
{
    public function index(){
        $data = Galeri::where('bidang', 'kader pokja 3')->where('status', 'proses')
        ->get();
        return view('backend.galerilaporanpokja3', compact('data'));
    }

    public function edit(string $id)
    {
        $data = Galeri::find($id);
        return view('backend.tampil_galerilaporanpokja3', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $data = Galeri::find($id);
            $data->update([
                'deskripsi' => $request->deskripsi,
                'tanggal' => $request->tanggal,
                'status' => 'upload',
            ]);
        return redirect()->route('galerilaporanpokja3.index')->with(['success' => 'Berhasil Menambahkan Galeri']);
    }
    
     public function destroy($gambar)
    {
        $deletedRows = Galeri::where('gambar', $gambar)->delete();

    if ($deletedRows > 0) {

        return redirect()->route('galerilaporanpokja3.index')->with(['success' => 'Berhasil Menghapus Gambar dalam Galeri']);
    }
    }

}
