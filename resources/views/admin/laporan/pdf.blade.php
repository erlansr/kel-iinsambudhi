{{-- resources/views/admin/laporan/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas RT - {{ $bulanTahun }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header h3 {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary th, .summary td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .summary th {
            background-color: #f2f2f2;
        }
        .summary .total {
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data th, table.data td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.data th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }
        table.data td {
            text-align: center;
        }
        table.data td.text-left {
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .lunas {
            color: green;
            font-weight: bold;
        }
        .belum-lunas {
            color: red;
            font-weight: bold;
        }
        .status-lunas {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
        }
        .status-belum {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏘️ LAPORAN KAS RT</h1>
        <h3>Periode: {{ $bulanTahun }}</h3>
        <p>Status: {{ $status == 'all' ? 'Semua' : ($status == 'lunas' ? 'Lunas' : 'Belum Lunas') }}</p>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="50%"><strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}</td>
                <td width="50%"><strong>Total Keluarga:</strong> {{ $totalKeluarga }}</td>
            </tr>
        </table>
    </div>
    
    <div class="summary">
        <table>
            <tr>
                <th>Total Tagihan</th>
                <th>Sudah Lunas</th>
                <th>Belum Lunas</th>
                <th>Tingkat Ketertiban</th>
            </tr>
            <tr>
                <td class="total">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                <td class="total" style="color: green">Rp {{ number_format($totalLunas, 0, ',', '.') }}</td>
                <td class="total" style="color: red">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</td>
                <td class="total">{{ number_format($persentase, 1) }}%</td>
            </tr>
            <tr>
                <td>{{ $totalKeluarga }} Keluarga</td>
                <td>{{ $totalLunasCount }} Keluarga</td>
                <td>{{ $totalBelumLunasCount }} Keluarga</td>
                <td></td>
            </tr>
        </table>
    </div>
    
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Keluarga</th>
                <th width="15%">Bulan</th>
                <th width="15%">Nominal</th>
                <th width="15%">Status</th>
                <th width="25%">Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayaran as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->keluarga->nama_keluarga }}</td>
                <td class="text-center">{{ $item->bulan }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($item->status == 'lunas')
                        <span class="status-lunas">✅ LUNAS</span>
                    @else
                        <span class="status-belum">🔴 BELUM LUNAS</span>
                    @endif
                </td>
                <td class="text-center">
                    {{ $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('d/m/Y H:i') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data tagihan untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
            <tr style="background-color: #e8f5e9; font-weight: bold;">
                <td colspan="3" class="text-right">LUNAS</td>
                <td class="text-right" style="color: green">Rp {{ number_format($totalLunas, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
            <tr style="background-color: #ffebee; font-weight: bold;">
                <td colspan="3" class="text-right">BELUM LUNAS</td>
                <td class="text-right" style="color: red">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }} | Sistem Kas RT</p>
        <p>*Laporan ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>
</html>