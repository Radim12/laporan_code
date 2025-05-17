<?php

namespace App\Http\Controllers\backend;

use App\Models\Berita;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class InputBeritaController extends Controller
{
    public function index()
    {
        $data = Berita::latest()->paginate();
        return view('backend.input_berita', compact('data'));
    }

    public function create() {}
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'judul' => 'required',
            'deskripsi' => 'required',
            'file' => 'nullable|file',
        ]);

        try {
            // Buat direktori jika belum ada
            if (!file_exists('storage/berita')) {
                mkdir('storage/berita', 0777, true);
            }
            if (!file_exists('storage/berita/file')) {
                mkdir('storage/berita/file', 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->image->move('storage/berita', $imageName);

            $data = [
                'image' => $imageName,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($request->hasFile('file')) {
                $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
                $request->file->move('storage/berita/file', $fileName);
                $data['file'] = $fileName;
            }

            Berita::create($data);

            return redirect()->route('input_berita.index')
                ->with(['success' => 'Berhasil Menambahkan Berita']);
        } catch (\Exception $e) {
            return back()->withInput()
                ->with(['error' => 'Gagal menyimpan berita: ' . $e->getMessage()]);
        }
    }

    public function show(string $id) {}

    public function update(Request $request, string $id)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'judul' => 'required',
            'deskripsi' => 'required',
            'file' => 'nullable|file',
        ]);

        try {
            $data = Berita::findOrFail($id);
            $updateData = [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'updated_at' => now(),
            ];

            // menangani update gambar
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move('storage/berita', $imageName);
                $updateData['image'] = $imageName;

                // Hapus gambar lama jika ada
                if ($data->image && file_exists('storage/berita/' . $data->image)) {
                    unlink('storage/berita/' . $data->image);
                }
            }

            // Menangani update file
            if ($request->hasFile('file')) {
                // Buat direktori untuk file jika belum ada
                if (!file_exists('storage/berita/file')) {
                    mkdir('storage/berita/file', 0777, true);
                }

                $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->move('storage/berita/file', $fileName);
                $updateData['file'] = $fileName;

                // Hapus file lama jika ada
                if ($data->file && file_exists('storage/berita/file/' . $data->file)) {
                    unlink('storage/berita/file/' . $data->file);
                }
            }

            $data->update($updateData);

            return redirect()->route('input_berita.index')
                ->with(['success' => 'Berhasil Mengedit Berita']);
        } catch (\Exception $e) {
            return back()->withInput()
                ->with(['error' => 'Gagal mengupdate berita: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        $data = Berita::find($id);
        return view('backend.tampil_berita', compact('data'));
    }

    public function destroy(string $id)

    {
        $data = Berita::find($id);

        $data->delete();

        return redirect()->route('input_berita.index')->with(['success' => 'Berhasil Menghapus Berita']);
    }
}
