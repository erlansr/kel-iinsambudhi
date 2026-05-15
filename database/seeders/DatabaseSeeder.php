<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Pembayaran;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Keluarga
        $keluarga = [
            ['nama_keluarga' => 'Bapak Ahmad Suhendar', 'alamat' => 'RT 01 RW 02 No 15', 'no_telepon' => '081234567890'],
            ['nama_keluarga' => 'Bapak Budi Santoso', 'alamat' => 'RT 01 RW 02 No 16', 'no_telepon' => '081234567891'],
            ['nama_keluarga' => 'Ibu Citra Dewi', 'alamat' => 'RT 01 RW 02 No 17', 'no_telepon' => '081234567892'],
            ['nama_keluarga' => 'Bapak Dedi Mulyadi', 'alamat' => 'RT 01 RW 02 No 18', 'no_telepon' => '081234567893'],
            ['nama_keluarga' => 'Ibu Eka Fitriani', 'alamat' => 'RT 01 RW 02 No 19', 'no_telepon' => '081234567894'],
        ];
        
        foreach ($keluarga as $k) {
            $keluargaModel = Keluarga::create($k);
            
            // Generate tagihan untuk 3 bulan terakhir
            $months = ['Januari 2025', 'Februari 2025', 'Maret 2025'];
            foreach ($months as $month) {
                Pembayaran::create([
                    'keluarga_id' => $keluargaModel->id,
                    'bulan' => $month,
                    'nominal' => 100000,
                    'status' => 'belum_lunas'
                ]);
            }
        }
    }
}