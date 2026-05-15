@extends('layouts.app')

@section('title', 'Pilih Keluarga')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">
        🏠 Sistem Kas RT
    </h1>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Pilih Nama Keluarga</h2>
        
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($keluargaList as $keluarga)
            <a href="{{ route('user.tagihan', $keluarga) }}" 
               class="block p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition border border-gray-200">
                <div class="font-semibold text-lg">{{ $keluarga->nama_keluarga }}</div>
                <div class="text-sm text-gray-600 mt-1">
                    📍 {{ $keluarga->alamat ?: 'Alamat tidak diisi' }}
                </div>
                <div class="text-sm text-gray-600">
                    📞 {{ $keluarga->no_telepon ?: '-' }}
                </div>
            </a>
            @endforeach
        </div>
        
        @if($keluargaList->isEmpty())
            <div class="text-center text-gray-500 py-8">
                Belum ada data keluarga. Silakan hubungi admin.
            </div>
        @endif
    </div>
</div>
@endsection