<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Prefix SKU untuk auto-generate kode barang (contoh: FURN, ELEC, TENT)
            $table->string('sku_prefix', 10)->nullable()->after('name')
                  ->comment('Prefix untuk auto-generate SKU barang, contoh: FURN, ELEC');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sku_prefix');
        });
    }
};
