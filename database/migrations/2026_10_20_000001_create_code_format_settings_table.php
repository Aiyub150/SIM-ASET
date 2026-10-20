<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_format_settings', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 50)->unique(); // 'item_sku', 'loan_code', 'stock_bast'
            $table->string('name', 100);             // Nama format
            $table->string('prefix', 30)->nullable();
            $table->string('date_format', 30)->nullable(); // 'YYYYMMDD', 'YYYY/MM', 'YYYY', null
            $table->string('suffix_type', 30)->default('INCREMENT_3_DIGIT'); // 'INCREMENT_3_DIGIT', 'INCREMENT_4_DIGIT', 'RANDOM_ALPHANUMERIC_5', 'CUSTOM'
            $table->string('custom_suffix', 50)->nullable();
            $table->string('separator', 5)->default('-');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_format_settings');
    }
};
