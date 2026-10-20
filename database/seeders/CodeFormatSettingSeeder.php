<?php

namespace Database\Seeders;

use App\Models\CodeFormatSetting;
use Illuminate\Database\Seeder;

class CodeFormatSettingSeeder extends Seeder
{
    public function run(): void
    {
        CodeFormatSetting::updateOrCreate(
            ['entity' => 'item_sku'],
            [
                'name'          => 'Format Kode SKU Barang Inventaris',
                'prefix'        => null, // Dinamis dari inisial kategori
                'date_format'   => null,
                'suffix_type'   => 'INCREMENT_3_DIGIT',
                'custom_suffix' => null,
                'separator'     => '-',
                'description'   => 'Pola kode inventaris aset daerah. Prefix diambil dari inisial kategori barang diikuti nomor urut (contoh: ELEK-001).',
            ]
        );

        CodeFormatSetting::updateOrCreate(
            ['entity' => 'loan_code'],
            [
                'name'          => 'Format Kode Transaksi Peminjaman',
                'prefix'        => 'PJM',
                'date_format'   => 'YYYYMMDD',
                'suffix_type'   => 'RANDOM_ALPHANUMERIC_5',
                'custom_suffix' => null,
                'separator'     => '-',
                'description'   => 'Nomor bukti transaksi peminjaman barang. Menggabungkan prefix, tanggal transaksi, dan 5 karakter acak unik verifikasi keamanan (contoh: PJM-20261020-RRBQA).',
            ]
        );

        CodeFormatSetting::updateOrCreate(
            ['entity' => 'stock_bast'],
            [
                'name'          => 'Format Berita Acara Mutasi Stok (BAST)',
                'prefix'        => 'BAST',
                'date_format'   => 'YYYY/MM',
                'suffix_type'   => 'INCREMENT_3_DIGIT',
                'custom_suffix' => null,
                'separator'     => '/',
                'description'   => 'Nomor registrasi dokumen resmi Berita Acara Serah Terima mutasi stok masuk/keluar (contoh: BAST/2026/10/001).',
            ]
        );
    }
}
