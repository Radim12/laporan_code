<?php

namespace App\Http\Controllers\backend;

use App\Models\Pengumuman;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class InputPengumumanController extends Controller
{
    public function index(): View
    {
        $pengu = Pengumuman::latest()->paginate(10);
        return view('backend.input_pengumuman', compact('pengu'));
    }

    public function create(): View
    {
        return view('backend.create_pengumuman');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'judulPengumuman' => 'required|string|max:255',
            'deskripsiPengumuman' => 'required|string',
            'tempatPengumuman' => 'required|string|max:255',
            'tanggalPengumuman' => 'required|date',
        ]);

        Pengumuman::create([
            'judulPengumuman' => $request->judulPengumuman,
            'deskripsiPengumuman' => $request->deskripsiPengumuman,
            'tempatPengumuman' => $request->tempatPengumuman,
            'tanggalPengumuman' => $request->tanggalPengumuman,
        ]);

        return redirect()->route('input_pengumuman.index')
            ->with('success', 'Berhasil Menambahkan Pengumuman');
    }

    public function show(string $id): View
    {
        $pengu = Pengumuman::findOrFail($id);
        return view('backend.show_pengumuman', compact('pengu')); // Create this view for showing details
    }

    public function edit(string $id): View
    {
        $pengu = Pengumuman::findOrFail($id);
        return view('backend.tampil_pengumuman', compact('pengu'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'judulPengumuman' => 'required|string|max:255',
            'deskripsiPengumuman' => 'required|string',
            'tempatPengumuman' => 'required|string|max:255',
            'tanggalPengumuman' => 'required|date',
        ]);

        $pengu = Pengumuman::findOrFail($id);
        $pengu->update([
            'judulPengumuman' => $request->judulPengumuman,
            'deskripsiPengumuman' => $request->deskripsiPengumuman,
            'tempatPengumuman' => $request->tempatPengumuman,
            'tanggalPengumuman' => $request->tanggalPengumuman,
        ]);

        return redirect()->route('input_pengumuman.index')
            ->with('success', 'Berhasil Mengedit Pengumuman');
    }

    public function destroy(string $id): RedirectResponse
    {
        $pengu = Pengumuman::findOrFail($id);
        $pengu->delete();

        return redirect()->route('input_pengumuman.index')
            ->with('success', 'Berhasil Menghapus Pengumuman');
    }
}
