@extends('layouts.admin')

@section('title', 'Tambah Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">➕ Tambah Tagihan</h1>
        <a href="{{ route('admin.tagihan') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>
    
    <form method="POST" action="{{ route('admin.tagihan.store') }}">
        @csrf
        
        <div class="grid gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Pilih Keluarga *</label>
                <select name="keluarga_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Pilih Keluarga --</option>
                    @foreach($keluarga as $k)
                        <option value="{{ $k->id }}" {{ old('keluarga_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_keluarga }} - {{ $k->alamat ?: 'Alamat tidak diisi' }}
                        </option>
                    @endforeach
                </select>
                @error('keluarga_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Bulan *</label>
                <input type="text" name="bulan" value="{{ old('bulan') }}" 
                       placeholder="Contoh: Januari 2025"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                       required>
                @error('bulan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nominal (Rp) *</label>
                <input type="number" name="nominal" value="{{ old('nominal', 100000) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                       required>
                @error('nominal')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                💾 Simpan Tagihan
            </button>
        </div>
    </form>
</div>
@endsection