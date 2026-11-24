<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catatan;

class CatatanController extends Controller
{
    public function index()
{
    $catatan = Catatan::all();

    return view('catatan.index', compact('catatan'));
}

    public function store(Request $request)
    {
        Catatan::create($request->only(['judul', 'isi']));

        return redirect('/catatan')->with('sukses', 'Catatan berhasil disimpan!');
    }

    public function create()
{
    return view('catatan.create');
}

public function show($id)
{
    $catatan = Catatan::findOrFail($id);

    return view('catatan.show', compact('catatan'));
}

public function edit($id)
{
    $catatan = Catatan::findOrFail($id);

    return view('catatan.edit', compact('catatan'));
}

public function update(Request $request, $id)
{
    $catatan = Catatan::findOrFail($id);
    $catatan->update($request->only(['judul', 'isi']));

    return redirect("/catatan/{$id}")->with('sukses', 'Catatan berhasil diperbarui!');
}

public function destroy($id)
{
    $catatan = Catatan::findOrFail($id);
    $catatan->delete();

    return redirect('/catatan')->with('sukses', 'Catatan berhasil dihapus!');
}
}