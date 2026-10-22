<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan barang dummy bawaan seeder jika belum ada transaksi peminjaman aktif
        $dummySkus = ['FURN-001', 'ELEC-001', 'TENT-001'];
        $dummyItemIds = DB::table('items')->whereIn('sku', $dummySkus)->pluck('id')->toArray();

        if (!empty($dummyItemIds)) {
            // Cek apakah ada loan_items terkait
            if (Schema::hasTable('loan_items')) {
                $hasActiveLoans = DB::table('loan_items')->whereIn('item_id', $dummyItemIds)->exists();
                if (!$hasActiveLoans) {
                    if (Schema::hasTable('stock_movements')) {
                        DB::table('stock_movements')->whereIn('item_id', $dummyItemIds)->delete();
                    }
                    DB::table('items')->whereIn('id', $dummyItemIds)->delete();
                }
            }
        }

        // 2. Bersihkan instansi dummy bawaan seeder jika belum ada relasi loans
        $dummyBorrowerNames = ['Dinas Pendidikan', 'Dinas Kesehatan'];
        $dummyBorrowerIds = DB::table('borrowers')->whereIn('institution_name', $dummyBorrowerNames)->pluck('id')->toArray();

        if (!empty($dummyBorrowerIds)) {
            if (Schema::hasTable('loans')) {
                $hasLoans = DB::table('loans')->whereIn('borrower_id', $dummyBorrowerIds)->exists();
                if (!$hasLoans) {
                    DB::table('borrowers')->whereIn('id', $dummyBorrowerIds)->delete();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
