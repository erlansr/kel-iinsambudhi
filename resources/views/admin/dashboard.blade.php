@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Total Keluarga</div>
        <div class="text-3xl font-bold text-gray-800">{{ $totalKeluarga }}</div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Tagihan Belum Lunas</div>
        <div class="text-3xl font-bold text-red-600">{{ $totalTagihan }}</div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Sudah Lunas</div>
        <div class="text-3xl font-bold text-green-600">{{ $totalLunas }}</div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Total Pendapatan</div>
        <div class="text-3xl font-bold text-blue-600">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Pembayaran Terbaru</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Keluarga</th>
                    <th class="px-4 py-2 text-left">Bulan</th>
                    <th class="px-4 py-2 text-right">Nominal</th>
                    <th class="px-4 py-2 text-center">Metode</th>
                    <th class="px-4 py-2 text-center">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestPayments as $payment)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $payment->keluarga->nama_keluarga }}</td>
                    <td class="px-4 py-2">{{ $payment->bulan }}</td>
                    <td class="px-4 py-2 text-right">
                        Rp {{ number_format($payment->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($payment->payment_method == 'qris')
                            <span class="text-blue-600">📱 QRIS</span>
                        @else
                            <span class="text-gray-600">✍️ Manual</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center text-sm">
                        {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">Belum ada data pembayaran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection