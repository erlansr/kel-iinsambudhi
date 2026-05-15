{{-- resources/views/admin/laporan/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📊 Laporan Keuangan</h1>
        <div class="space-x-2">
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                🖨️ Cetak
            </button>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.laporan') }}" class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 border rounded-lg">
                    @foreach($bulanList as $b)
                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                            {{ $b }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tahun</label>
                <select name="tahun" class="w-full px-3 py-2 border rounded-lg">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="lunas" {{ $status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_lunas" {{ $status == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    🔍 Tampilkan
                </button>
                <a href="{{ route('admin.laporan.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'status' => $status]) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition whitespace-nowrap">
                    📄 PDF
                </a>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm">Total Tagihan</div>
            <div class="text-2xl font-bold text-blue-800">
                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
            </div>
            <div class="text-xs text-blue-600 mt-1">{{ $totalKeluarga }} Keluarga</div>
        </div>
        
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-green-600 text-sm">Sudah Lunas</div>
            <div class="text-2xl font-bold text-green-800">
                Rp {{ number_format($totalLunas, 0, ',', '.') }}
            </div>
            <div class="text-xs text-green-600 mt-1">{{ $totalLunasCount }} Keluarga</div>
        </div>
        
        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <div class="text-red-600 text-sm">Belum Lunas</div>
            <div class="text-2xl font-bold text-red-800">
                Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}
            </div>
            <div class="text-xs text-red-600 mt-1">{{ $totalBelumLunasCount }} Keluarga</div>
        </div>
        
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <div class="text-purple-600 text-sm">Tingkat Ketertiban</div>
            <div class="text-2xl font-bold text-purple-800">
                {{ number_format($persentase, 1) }}%
            </div>
            <div class="text-xs text-purple-600 mt-1">Dari total tagihan</div>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="mb-6">
        <div class="flex justify-between text-sm mb-1">
            <span>Progres Pembayaran</span>
            <span>{{ number_format($persentase, 1) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-green-600 h-4 rounded-full" style="width: {{ $persentase }}%"></div>
        </div>
    </div>
    
    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Keluarga</th>
                    <th class="px-4 py-3 text-left">Bulan</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="px-4 py-3 text-right">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Tgl Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $index => $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->keluarga->nama_keluarga }}</td>
                    <td class="px-4 py-3">{{ $item->bulan }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->bulan }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($item->status == 'lunas')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">✅ LUNAS</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">🔴 BELUM LUNAS</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm">
                        {{ $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        Tidak ada data tagihan untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right">Total</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right">Lunas</td>
                    <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($totalLunas, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right">Belum Lunas</td>
                    <td class="px-4 py-3 text-right text-red-600">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Export Buttons -->
    <div class="mt-6 flex justify-end space-x-3">
        <button onclick="exportToCSV()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
            📥 Export CSV
        </button>
        <a href="{{ route('admin.laporan.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'status' => $status]) }}" 
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
            📄 Export PDF
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            🖨️ Print
        </button>
    </div>
</div>

<script>
function exportToCSV() {
    let csv = [];
    let rows = document.querySelectorAll('table tr');
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/,/g, ';');
            row.push(text);
        }
        
        csv.push(row.join(','));
    }
    
    let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    let link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'laporan_kas_rt_{{ $bulan }}_{{ $tahun }}.csv';
    link.click();
}

// Style untuk print
var style = document.createElement('style');
style.innerHTML = `
    @media print {
        .no-print {
            display: none !important;
        }
        button, .bg-gray-600, .bg-green-600, .bg-blue-600, .bg-red-600 {
            display: none !important;
        }
        nav, .bg-green-700 {
            display: none !important;
        }
        body {
            padding: 0;
            margin: 0;
        }
        .container {
            max-width: 100%;
            padding: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection