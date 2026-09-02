<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndAdminSeeder::class, // roles + users dulu
            MasterDataSeeder::class,   // data master (lokasi, kategori, barang, borrower)
        ]);
    }
}
