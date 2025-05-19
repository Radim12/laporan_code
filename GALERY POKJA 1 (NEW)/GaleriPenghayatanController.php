<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Galeri;
use App\Http\Controllers\Controller;

class GaleriPenghayatanController extends Controller
{
    public function index(){
        $data = Galeri::where('bidang', 'penghayatan & pengamalan pancasila')->where('status', 'Proses')
        ->get();
        return view('backend.galeripenghayatan', compact('data'));
    }

    public function edit(string $id)
    {
        $data = Galeri::all()->find($id);
        return view('backend.tampil_galeripenghayatan', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $data = Galeri::find($id);
            $data->update([
                'deskripsi' => $request->deskripsi,
                'tanggal' => $request->tanggal,
                'status' => 'upload',
            ]);
        return redirect()->route('galeripenghayatan.index')->with(['success' => 'Berhasil Menambahkan Galeri']);
    }

    public function destroy($gambar)
    {
        $deletedRows = Galeri::where('gambar', $gambar)->delete();

    if ($deletedRows > 0) {

        return redirect()->route('galeripenghayatan.index')->with(['success' => 'Berhasil Menghapus Gambar dalam Galeri']);
    }
    }
    
}
