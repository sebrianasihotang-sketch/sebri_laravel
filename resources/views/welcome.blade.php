@extends('layouts.app')

@section('title', $judul)

@section('content')
<div class="mt-4">
    <h5>Daftar Menu Latihan:</h5>
    <ul class="list-group">
        @foreach($menu as $item)
            <li class="list-group-item">{{ $item }}</li>
        @endforeach
       <li $kontak = 'email'   => sebriana@gmail.com</li>
       <li $kontak = 'telepon' => 0821-7777-2102</li>

    </ul>
</div>
@endsection