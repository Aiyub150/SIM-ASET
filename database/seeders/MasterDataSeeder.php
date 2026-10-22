<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Category;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Master Lokasi dasar jika belum ada
        Location::firstOrCreate(
            ['name' => 'Gudang Utama Pemkab'],
            ['address' => 'Jl. Pemuda No. 1']
        );
        Location::firstOrCreate(
            ['name' => 'Gudang Aula Serbaguna'],
            ['address' => 'Lantai 1 Gedung A']
        );

        // 2. Buat Master Kategori dasar jika belum ada (sku_prefix untuk auto-generate SKU)
        Category::firstOrCreate(
            ['name' => 'Furnitur'],
            ['description' => 'Meja, Kursi, Lemari', 'sku_prefix' => 'FURN']
        );
        Category::firstOrCreate(
            ['name' => 'Elektronik'],
            ['description' => 'Proyektor, Sound System, Mic', 'sku_prefix' => 'ELEC']
        );
        Category::firstOrCreate(
            ['name' => 'Perlengkapan Acara'],
            ['description' => 'Tenda, Panggung, Karpet', 'sku_prefix' => 'TENT']
        );

        // Data dummy barang dan data instansi dihilangkan agar sistem bersih dari data dummy yang tidak bisa dihapus.
    }
}
