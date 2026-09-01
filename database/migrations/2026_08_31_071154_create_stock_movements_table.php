<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique()->comment('Nomor Surat/Faktur/BAST');
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->comment('Admin penanggung jawab');
            
            // type: 'in' (pengadaan), 'out' (dihibahkan), 'broken' (rusak), 'lost' (hilang)
            $table->enum('type', ['in', 'out', 'broken', 'lost']); 
            
            $table->unsignedInteger('qty')->comment('Jumlah mutasi');
            $table->unsignedInteger('balance_before')->comment('Stok sebelum mutasi');
            $table->unsignedInteger('balance_after')->comment('Stok setelah mutasi');
            $table->text('notes')->nullable();
            
            // Timestamps akan mencatat kapan mutasi ini terjadi (created_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
