<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use App\Models\Category;
use App\Models\Item;
use App\Models\Borrower;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        $admin = User::create([
            'name' => 'Admin Pemda',
            'email' => 'admin@pemda.go.id',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat Master Lokasi
        $gudangUtama = Location::create(['name' => 'Gudang Utama Pemkab', 'address' => 'Jl. Pemuda No. 1']);
        $aula = Location::create(['name' => 'Gudang Aula Serbaguna', 'address' => 'Lantai 1 Gedung A']);

        // 3. Buat Master Kategori
        $furnitur = Category::create(['name' => 'Furnitur', 'description' => 'Meja, Kursi, Lemari']);
        $elektronik = Category::create(['name' => 'Elektronik', 'description' => 'Proyektor, Sound System, Mic']);
        $perlengkapan = Category::create(['name' => 'Perlengkapan Acara', 'description' => 'Tenda, Panggung, Karpet']);

        // 4. Buat Master Barang
        // Kritis: Karena ini data awal, total_qty dan available_qty WAJIB sama.
        Item::create([
            'sku' => 'FURN-001',
            'name' => 'Kursi Lipat Chitose',
            'category_id' => $furnitur->id,
            'location_id' => $gudangUtama->id,
            'total_qty' => 500,
            'available_qty' => 500,
        ]);

        Item::create([
            'sku' => 'ELEC-001',
            'name' => 'Proyektor Epson EB-X51',
            'category_id' => $elektronik->id,
            'location_id' => $aula->id,
            'total_qty' => 10,
            'available_qty' => 10,
        ]);

        Item::create([
            'sku' => 'TENT-001',
            'name' => 'Tenda Pleton 6x14m',
            'category_id' => $perlengkapan->id,
            'location_id' => $gudangUtama->id,
            'total_qty' => 5,
            'available_qty' => 5,
        ]);

        // 5. Buat Master Peminjam (Borrowers)
        Borrower::create([
            'institution_name' => 'Dinas Pendidikan',
            'pic_name' => 'Budi Santoso',
            'contact_number' => '081234567890',
            'address' => 'Gedung B Lantai 2'
        ]);

        Borrower::create([
            'institution_name' => 'Dinas Kesehatan',
            'pic_name' => 'Siti Aminah',
            'contact_number' => '089876543210',
            'address' => 'Gedung C Lantai 1'
        ]);
    }
}
