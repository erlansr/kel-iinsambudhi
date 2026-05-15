@extends('layouts.admin')

@section('title', 'Edit Keluarga')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">✏️ Edit Keluarga</h1>
        <a href="{{ route('admin.keluarga') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>
    
    <form method="POST" action="{{ route('admin.keluarga.update', $keluarga) }}">
        @csrf
        @method('PUT')
        
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nama Kepala Keluarga *</label>
                <input type="text" name="nama_keluarga" value="{{ old('nama_keluarga', $keluarga->nama_keluarga) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                       required>
                @error('nama_keluarga')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nomor KK</label>
                <input type="text" name="no_kk" value="{{ old('no_kk', $keluarga->no_kk) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                @error('no_kk')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">No. Telepon</label>
                <input type="text" name="no_telepon" value="{{ old('no_telepon', $keluarga->no_telepon) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                @error('no_telepon')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                <textarea name="alamat" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">{{ old('alamat', $keluarga->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                💾 Update Keluarga
            </button>
        </div>
    </form>
</div>
@endsection