{{-- resources/views/user/tagihan.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ url('/') }}" class="text-green-600 mb-4 inline-block">← Kembali</a>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-2">{{ $keluarga->nama_keluarga }}</h1>
        <p class="text-gray-600 mb-6">{{ $keluarga->alamat ?: 'Alamat belum diisi' }}</p>
        
        @php
            $tagihanAktif = $pembayaran->where('status', 'belum_lunas');
        @endphp
        
        @if($tagihanAktif->count() > 0)
            @foreach($tagihanAktif as $tagihan)
            <div class="border rounded-lg p-4 mb-3 flex justify-between items-center hover:bg-gray-50 transition">
                <div>
                    <div class="font-semibold text-lg">{{ $tagihan->bulan }}</div>
                    <div class="text-gray-600">
                        Rp {{ number_format((int)$tagihan->nominal, 0, ',', '.') }}
                    </div>
                </div>
                
                <button onclick="bayar({{ $tagihan->id }}, this)" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                    💳 Bayar
                </button>
            </div>
            @endforeach
        @else
            <div class="text-center py-12 bg-green-50 rounded-lg">
                <div class="text-6xl mb-4">🎉</div>
                <h3 class="text-xl font-semibold text-green-600">Semua Tagihan Lunas!</h3>
                <p class="text-gray-500 mt-2">Terima kasih, tidak ada tagihan yang perlu dibayar.</p>
                <a href="{{ url('/') }}" class="inline-block mt-4 text-blue-500 hover:underline">← Ganti keluarga</a>
            </div>
        @endif
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
function bayar(id, btn) {
    // Disable button
    btn.disabled = true;
    btn.innerHTML = '⏳ Memproses...';
    
    fetch('/generate-qris/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Buka popup Midtrans
            snap.pay(data.token, {
                onSuccess: function(result) {
                    alert('✅ Pembayaran berhasil!');
                    window.location.reload();
                },
                onPending: function(result) {
                    alert('⏳ Pembayaran pending, silakan selesaikan pembayaran');
                },
                onError: function(result) {
                    alert('❌ Gagal: ' + (result.status_message || 'Unknown error'));
                    btn.disabled = false;
                    btn.innerHTML = '💳 Bayar';
                },
                onClose: function() {
                    btn.disabled = false;
                    btn.innerHTML = '💳 Bayar';
                }
            });
        } else {
            alert('❌ Error: ' + (data.error || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = '💳 Bayar';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Gagal koneksi: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '💳 Bayar';
    });
}
</script>
@endsection