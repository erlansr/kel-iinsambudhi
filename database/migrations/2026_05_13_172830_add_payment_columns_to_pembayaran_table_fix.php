<?php
// database/migrations/2026_05_14_000002_add_payment_columns_to_pembayaran_table_fix.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Tambah kolom payment_method jika belum ada
            if (!Schema::hasColumn('pembayaran', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_at');
            }
            
            // Tambah kolom payment_type jika belum ada
            if (!Schema::hasColumn('pembayaran', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_method');
            }
            
            // Tambah kolom qris_expiry jika belum ada
            if (!Schema::hasColumn('pembayaran', 'qris_expiry')) {
                $table->timestamp('qris_expiry')->nullable()->after('payment_type');
            }
        });
    }

    public function down()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_type', 'qris_expiry']);
        });
    }
};