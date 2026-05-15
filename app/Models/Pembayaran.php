<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'keluarga_id',
        'bulan',
        'nominal',
        'status',
        'transaction_id',
        'midtrans_order_id',
        'paid_at',
        'payment_method',
        'payment_type',
        'qris_expiry'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'qris_expiry' => 'datetime',
        'nominal' => 'integer'  // ← UBAH DARI decimal:2 JADI integer
    ];

    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function markAsPaid($transactionId = null, $method = 'qris')
    {
        $this->update([
            'status' => 'lunas',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
            'payment_method' => $method
        ]);
    }
    
    // Tambahkan accessor untuk format Rupiah
    public function getNominalRupiahAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}