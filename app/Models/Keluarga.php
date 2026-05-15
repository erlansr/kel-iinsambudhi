<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Keluarga extends Model
{
    protected $table = 'keluarga';
    
    protected $fillable = [
        'nama_keluarga',
        'alamat',
        'no_kk',
        'no_telepon'
    ];
    
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
    
    public function getTagihanBulanIniAttribute()
    {
        $bulanIni = date('F Y');
        return $this->pembayaran()->where('bulan', $bulanIni)->first();
    }
}