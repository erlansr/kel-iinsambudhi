@extends('layouts.admin')

@section('title', 'Data Keluarga')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">👨‍👩‍👧‍👦 Data Keluarga</h1>
        <a href="{{ route('admin.keluarga.create') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
            + Tambah Keluarga
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Keluarga</th>
                    <th class="px-4 py-3 text-left">No. KK</th>
                    <th class="px-4 py-3 text-left">No. Telepon</th>
                    <th class="px-4 py-3 text-left">Alamat</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keluarga as $index => $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $keluarga->firstItem() + $index }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->nama_keluarga }}</td>
                    <td class="px-4 py-3">{{ $item->no_kk ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $item->no_telepon ?: '-' }}</td>
                    <td class="px-4 py-3">{{ Str::limit($item->alamat, 30) ?: '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.keluarga.edit', $item) }}" 
                           class="text-blue-600 hover:text-blue-800 mr-3">✏️ Edit</a>
                        <button onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_keluarga }}')" 
                                class="text-red-600 hover:text-red-800">🗑️ Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        Belum ada data keluarga. Silakan tambah keluarga baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $keluarga->links() }}
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-xl font-bold mb-4">Konfirmasi Hapus</h3>
        <p id="deleteMessage" class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus keluarga ini?</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteMessage').innerHTML = `Apakah Anda yakin ingin menghapus keluarga <strong>${name}</strong>?`;
    document.getElementById('deleteForm').action = `/admin/keluarga/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>
@endpush
@endsection