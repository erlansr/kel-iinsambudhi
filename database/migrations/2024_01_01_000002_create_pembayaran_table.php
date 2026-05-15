
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keluarga_id')->constrained('keluarga')->onDelete('cascade');
            $table->string('bulan', 15); // Januari 2024
            $table->decimal('nominal', 12, 2)->default(100000);
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->string('transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable(); // qris, manual
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['keluarga_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};