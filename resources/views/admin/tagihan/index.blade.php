{{-- resources/views/admin/tagihan/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Data Tagihan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Data Tagihan</h1>
        <div class="space-x-3">
            <a href="{{ route('admin.tagihan.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                + Tambah Tagihan
            </a>
            <a href="{{ route('admin.tagihan.bulk') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                📦 Generate Massal
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Keluarga</th>
                    <th class="px-4 py-3 text-left">Bulan</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihan as $index => $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $tagihan->firstItem() + $index }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->keluarga->nama_keluarga }}</td>
                    <td class="px-4 py-3">{{ $item->bulan }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format((int)$item->nominal, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($item->status == 'lunas')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">✅ LUNAS</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">🔴 BELUM LUNAS</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->status == 'belum_lunas')
                            <button onclick="markAsPaid({{ $item->id }})" 
                                    class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded text-sm transition">
                                ✅ Tandai Lunas
                            </button>
                        @endif
                        <a href="{{ route('admin.tagihan.edit', $item) }}" 
                           class="text-blue-600 hover:text-blue-800 mx-1 inline-block">✏️</a>
                        <button onclick="deleteTagihan({{ $item->id }})" 
                                class="text-red-600 hover:text-red-800">🗑️</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        Belum ada data tagihan. Silakan tambah tagihan baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $tagihan->links() }}
    </div>
</div>

<script>
function markAsPaid(id) {
    if (!confirm('✅ Tandai lunas tagihan ini?')) return;
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '⏳ ...';
    
    fetch('/admin/tagihan/' + id + '/mark-paid', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Tagihan berhasil ditandai lunas');
            location.reload();
        } else {
            alert('❌ Gagal: ' + (data.message || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = '✅ Tandai Lunas';
        }
    })
    .catch(error => {
        alert('❌ Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '✅ Tandai Lunas';
    });
}

function deleteTagihan(id) {
    if (!confirm('⚠️ Yakin hapus tagihan ini? Data tidak dapat dikembalikan!')) return;
    
    fetch('/admin/tagihan/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Tagihan berhasil dihapus');
            location.reload();
        } else {
            alert('❌ Gagal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('❌ Error: ' + error.message);
    });
}
</script>
@endsection