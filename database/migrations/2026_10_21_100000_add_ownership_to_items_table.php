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
        Schema::table('items', function (Blueprint $table) {
            $table->enum('ownership_status', ['internal', 'vendor', 'external'])
                ->default('internal')
                ->after('sku')
                ->comment('Status kepemilikan aset: internal (sendiri), vendor (sewa/pihak ketiga), external (instansi lain/pinjam pakai)');
            $table->string('owner_name')
                ->nullable()
                ->after('ownership_status')
                ->comment('Nama vendor rekanan atau instansi asal pemilik aset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['ownership_status', 'owner_name']);
        });
    }
};
