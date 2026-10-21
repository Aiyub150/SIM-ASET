<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan seluruh data terlebih dahulu (clean slate)
        Schema::disableForeignKeyConstraints();

        $tables = [
            'loan_items',
            'loans',
            'stock_movements',
            'activity_logs',
            'items',
            'categories',
            'locations',
            'borrowers',
            'code_format_settings',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'users',
            'roles',
            'permissions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        // 2. Jalankan seeder data awal
        $this->call([
            RoleAndAdminSeeder::class,      // roles + permissions + users bawaan
            MasterDataSeeder::class,        // data master (lokasi, kategori, barang, borrower)
            CodeFormatSettingSeeder::class, // pengaturan format SKU & kode peminjaman
        ]);
    }
}
