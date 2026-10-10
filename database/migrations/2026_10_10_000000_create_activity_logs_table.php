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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action'); // LOGIN, LOGOUT, CREATE, UPDATE, DELETE, STOCK_ADJUST, LOAN_CREATE, LOAN_RETURN
            $table->string('module'); // Autentikasi, Barang, Kategori, Peminjaman, Mutasi Stok, Pengguna, Sistem
            $table->text('description'); // Keterangan rinci aktivitas
            $table->json('properties')->nullable(); // Metadata payload / info tambahan
            $table->string('ip_address', 45)->nullable(); // IP asli client
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['module', 'action']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
