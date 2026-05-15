{{-- resources/views/admin/tagihan/bulk.blade.php --}}
@extends('layouts.admin')

@section('title', 'Generate Tagihan Massal')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📦 Generate Tagihan Massal</h1>
        <a href="{{ route('admin.tagihan') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">📌 Informasi:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Fitur ini akan membuat tagihan baru untuk periode yang dipilih</li>
                <li>Jika tagihan sudah ada untuk periode tersebut, akan dilewati (tidak double)</li>
                <li>Klik "Pilih Semua" untuk mencentang semua keluarga</li>
                <li>Klik "Hapus Semua" untuk membatalkan semua pilihan</li>
            </ul>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.tagihan.bulk') }}" id="bulkForm">
        @csrf
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Bagian Pilih Keluarga -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-3">👨‍👩‍👧‍👦 Pilih Keluarga</label>
                
                <!-- Tombol Aksi -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex space-x-3">
                        <button type="button" id="selectAllBtn" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                            <span>✅</span>
                            <span>Pilih Semua</span>
                        </button>
                        <button type="button" id="unselectAllBtn" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                            <span>❌</span>
                            <span>Hapus Semua</span>
                        </button>
                    </div>
                    <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded">
                        Dipilih: <span id="selectedCount" class="font-bold text-blue-600">0</span> dari <span id="totalCount" class="font-bold">{{ $keluarga->count() }}</span> keluarga
                    </div>
                </div>
                
                <!-- Daftar Checkbox Keluarga -->
                <div class="border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50">
                    <div class="grid md:grid-cols-2 gap-2">
                        @foreach($keluarga as $k)
                        <label class="flex items-center space-x-2 p-2 hover:bg-blue-100 rounded cursor-pointer transition">
                            <input type="checkbox" name="keluarga_ids[]" value="{{ $k->id }}" 
                                   class="family-checkbox w-4 h-4 rounded border-gray-300 focus:ring-blue-500">
                            <span class="text-sm">
                                {{ $k->nama_keluarga }}
                                <span class="text-gray-500 text-xs">({{ $k->no_telepon ?: 'no telp' }})</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                @if($keluarga->isEmpty())
                    <p class="text-red-500 text-sm mt-2">⚠️ Belum ada data keluarga. Silakan tambah keluarga terlebih dahulu.</p>
                @endif
            </div>
            
            <!-- Periode -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">📅 Bulan *</label>
                <select name="bulan" id="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <option value="">-- Pilih Bulan --</option>
                    <option value="Januari">Januari</option>
                    <option value="Februari">Februari</option>
                    <option value="Maret">Maret</option>
                    <option value="April">April</option>
                    <option value="Mei">Mei</option>
                    <option value="Juni">Juni</option>
                    <option value="Juli">Juli</option>
                    <option value="Agustus">Agustus</option>
                    <option value="September">September</option>
                    <option value="Oktober">Oktober</option>
                    <option value="November">November</option>
                    <option value="Desember">Desember</option>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">📅 Tahun *</label>
                <select name="tahun" id="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == date('Y') ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Nominal -->
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">💰 Nominal Tagihan (Rp) *</label>
                <div class="flex space-x-2">
                    <input type="number" name="nominal" id="nominal" value="100000" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <button type="button" onclick="setNominal(50000)" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">50.000</button>
                    <button type="button" onclick="setNominal(100000)" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">100.000</button>
                    <button type="button" onclick="setNominal(150000)" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">150.000</button>
                    <button type="button" onclick="setNominal(200000)" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">200.000</button>
                </div>
            </div>
        </div>
        
        <!-- Preview Card -->
        <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-gray-700 mb-3">📊 Preview Tagihan:</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700">📅 Periode: <strong id="previewPeriod" class="text-blue-600">-</strong></p>
                    <p class="text-gray-700 mt-2">💰 Nominal: <strong id="previewNominal" class="text-green-600">Rp 0</strong></p>
                </div>
                <div>
                    <p class="text-gray-700">👨‍👩‍👧‍👦 Jumlah Keluarga: <strong id="previewCount" class="text-orange-600">0</strong></p>
                    <p class="text-gray-700 mt-2">💵 Total Pendapatan: <strong id="previewTotal" class="text-purple-600">Rp 0</strong></p>
                </div>
            </div>
        </div>
        
        <!-- Tombol Submit -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.tagihan') }}" class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                Batal
            </a>
            <button type="submit" id="submitBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                🚀 Generate Tagihan
            </button>
        </div>
    </form>
</div>

<!-- JavaScript FIXS -->
<script>
// Ambil semua elemen yang dibutuhkan
const checkboxes = document.querySelectorAll('.family-checkbox');
const selectAllBtn = document.getElementById('selectAllBtn');
const unselectAllBtn = document.getElementById('unselectAllBtn');
const selectedCountSpan = document.getElementById('selectedCount');
const totalCountSpan = document.getElementById('totalCount');
const previewCountSpan = document.getElementById('previewCount');
const previewTotalSpan = document.getElementById('previewTotal');
const bulanSelect = document.getElementById('bulan');
const tahunSelect = document.getElementById('tahun');
const nominalInput = document.getElementById('nominal');
const previewPeriodSpan = document.getElementById('previewPeriod');
const previewNominalSpan = document.getElementById('previewNominal');

// Fungsi untuk update counter dan preview
function updateCounterAndPreview() {
    // Hitung checkbox yang dicentang
    let selected = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) selected++;
    });
    
    // Update counter
    selectedCountSpan.textContent = selected;
    previewCountSpan.textContent = selected;
    
    // Update total pendapatan
    let nominal = parseInt(nominalInput.value) || 0;
    let total = selected * nominal;
    previewTotalSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    // Update preview periode
    let bulan = bulanSelect.value;
    let tahun = tahunSelect.value;
    if (bulan && tahun) {
        previewPeriodSpan.textContent = bulan + ' ' + tahun;
    } else {
        previewPeriodSpan.textContent = '-';
    }
    
    // Update preview nominal
    previewNominalSpan.textContent = 'Rp ' + nominal.toLocaleString('id-ID');
}

// Fungsi Pilih Semua
function selectAll() {
    checkboxes.forEach(cb => {
        cb.checked = true;
    });
    updateCounterAndPreview();
    showToast('✅ Semua keluarga telah dipilih', 'success');
}

// Fungsi Hapus Semua
function unselectAll() {
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    updateCounterAndPreview();
    showToast('❌ Semua pilihan dihapus', 'info');
}

// Fungsi set nominal
function setNominal(value) {
    nominalInput.value = value;
    updateCounterAndPreview();
}

// Fungsi toast notifikasi sederhana
function showToast(message, type = 'success') {
    // Buat elemen toast
    let toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white shadow-lg z-50 transition-opacity duration-300';
    if (type === 'success') {
        toast.style.backgroundColor = '#22c55e';
    } else {
        toast.style.backgroundColor = '#3b82f6';
    }
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    // Hapus setelah 2 detik
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 2000);
}

// Event Listeners
if (selectAllBtn) {
    selectAllBtn.addEventListener('click', selectAll);
}
if (unselectAllBtn) {
    unselectAllBtn.addEventListener('click', unselectAll);
}
checkboxes.forEach(cb => {
    cb.addEventListener('change', updateCounterAndPreview);
});
bulanSelect.addEventListener('change', updateCounterAndPreview);
tahunSelect.addEventListener('change', updateCounterAndPreview);
nominalInput.addEventListener('input', updateCounterAndPreview);

// Initial update
updateCounterAndPreview();

// Validasi sebelum submit
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    let bulan = bulanSelect.value;
    let tahun = tahunSelect.value;
    let nominal = nominalInput.value;
    let selected = document.querySelectorAll('.family-checkbox:checked').length;
    
    if (!bulan) {
        e.preventDefault();
        alert('❌ Silakan pilih bulan terlebih dahulu!');
        return false;
    }
    
    if (!tahun) {
        e.preventDefault();
        alert('❌ Silakan pilih tahun terlebih dahulu!');
        return false;
    }
    
    if (!nominal || nominal <= 0) {
        e.preventDefault();
        alert('❌ Nominal harus lebih dari 0!');
        return false;
    }
    
    if (selected === 0) {
        e.preventDefault();
        alert('❌ Silakan pilih minimal 1 keluarga!');
        return false;
    }
    
    let total = selected * nominal;
    let konfirmasi = confirm(
        '📋 KONFIRMASI GENERATE TAGIHAN\n\n' +
        'Periode: ' + bulan + ' ' + tahun + '\n' +
        'Nominal: Rp ' + parseInt(nominal).toLocaleString('id-ID') + '\n' +
        'Jumlah Keluarga: ' + selected + '\n' +
        'Total Pendapatan: Rp ' + total.toLocaleString('id-ID') + '\n\n' +
        'Apakah Anda yakin ingin generate tagihan?'
    );
    
    if (!konfirmasi) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection