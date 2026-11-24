<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $judul = 'Selamat Datang di Les Laravel';
        $hari = 'Hari ke-5';
        $nama = 'Murid Laravel';

        return view('welcome', compact('judul', 'hari', 'nama'));
    }
}