<?php

namespace App\Http\Controllers;

use App\Models\CodeFormatSetting;
use App\Models\Item;
use App\Models\Loan;
use App\Models\StockMovement;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CodeFormatSettingController extends Controller
{
    /**
     * Tampilkan Pengaturan Format Kode SKU & Dokumen
     */
    public function index(): View
    {
        $settings = CodeFormatSetting::orderBy('id')->get();

        return view('administration.code-formats.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update Format Kode
     */
    public function update(Request $request, CodeFormatSetting $setting): RedirectResponse
    {
        $validated = $request->validate([
            'prefix'        => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9_\-\/]+$/'],
            'date_format'   => ['nullable', 'string', 'in:NONE,YYYYMMDD,YYYY-MM,YYYY/MM,YYYY'],
            'suffix_type'   => ['required', 'string', 'in:INCREMENT_3_DIGIT,INCREMENT_4_DIGIT,RANDOM_ALPHANUMERIC_5,CUSTOM'],
            'custom_suffix' => ['nullable', 'string', 'max:30', 'regex:/^[A-Za-z0-9_\-\/]+$/'],
            'separator'     => ['required', 'string', 'in:-,-,/,_'],
        ], [
            'prefix.regex'        => 'Format awalan (prefix) hanya boleh mengandung huruf, angka, minus, garis miring, atau underscore.',
            'custom_suffix.regex' => 'Format akhiran kustom hanya boleh mengandung huruf, angka, minus, garis miring, atau underscore.',
        ]);

        $dateFormat = $validated['date_format'] === 'NONE' ? null : $validated['date_format'];

        $oldConfig = [
            'prefix'        => $setting->prefix,
            'date_format'   => $setting->date_format,
            'suffix_type'   => $setting->suffix_type,
            'custom_suffix' => $setting->custom_suffix,
            'separator'     => $setting->separator,
            'preview'       => $setting->generatePreview(),
        ];

        $setting->update([
            'prefix'        => $validated['prefix'] ? trim($validated['prefix']) : null,
            'date_format'   => $dateFormat,
            'suffix_type'   => $validated['suffix_type'],
            'custom_suffix' => $validated['suffix_type'] === 'CUSTOM' ? trim($validated['custom_suffix']) : null,
            'separator'     => $validated['separator'],
        ]);

        $newConfig = [
            'prefix'        => $setting->prefix,
            'date_format'   => $setting->date_format,
            'suffix_type'   => $setting->suffix_type,
            'custom_suffix' => $setting->custom_suffix,
            'separator'     => $setting->separator,
            'preview'       => $setting->generatePreview(),
        ];

        // Sinkronisasi data riwayat massal jika dicentang oleh pengguna
        $syncedCount = 0;
        if ($request->boolean('sync_legacy_data')) {
            // Naikkan limit waktu dan memori agar pemrosesan ribuan data tidak terputus timeout
            @set_time_limit(0);
            @ini_set('max_execution_time', '0');
            @ini_set('memory_limit', '512M');
            $syncedCount = $this->syncLegacyRecords($setting);
        }

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Format Kode',
            "Mengubah konfigurasi pola kode untuk: {$setting->name}" . ($syncedCount > 0 ? " serta menyinkronkan {$syncedCount} rekaman data masa lalu." : ""),
            [
                'entity'       => $setting->entity,
                'before'       => $oldConfig,
                'after'        => $newConfig,
                'synced_count' => $syncedCount,
            ],
            $request
        );

        $successMsg = "Konfigurasi format untuk \"{$setting->name}\" berhasil disimpan.";
        if ($syncedCount > 0) {
            $successMsg .= " Sebanyak {$syncedCount} data lama berhasil disinkronkan ke pola kode baru.";
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Helper: Sinkronisasi data lama ke format penomoran baru
     */
    private function syncLegacyRecords(CodeFormatSetting $setting): int
    {
        return DB::transaction(function () use ($setting) {
            $count = 0;
            $sep = $setting->separator ?: '-';
            $padLen = ($setting->suffix_type === 'INCREMENT_4_DIGIT') ? 4 : 3;

            if ($setting->entity === 'item_sku') {
                $items = Item::with('category')->orderBy('id')->get();
                $seq = 1;
                foreach ($items as $item) {
                    $prefix = $setting->prefix ?: ($item->category->sku_prefix ?: strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $item->category->name ?? 'ASET'), 0, 4)));
                    $suffix = ($setting->suffix_type === 'RANDOM_ALPHANUMERIC_5') 
                        ? strtoupper(Str::random(5)) 
                        : ($setting->suffix_type === 'CUSTOM' ? ($setting->custom_suffix ?: '001') : str_pad($seq++, $padLen, '0', STR_PAD_LEFT));
                    
                    $newSku = $prefix . $sep . $suffix;
                    $item->update(['sku' => $newSku]);
                    $count++;
                }
            } elseif ($setting->entity === 'loan_code') {
                $loans = Loan::orderBy('id')->get();
                $seq = 1;
                foreach ($loans as $loan) {
                    $prefix = $setting->prefix ?: 'PJM';
                    $datePart = '';
                    $tgl = $loan->loan_date ?? $loan->created_at ?? now();
                    if ($setting->date_format === 'YYYYMMDD') {
                        $datePart = $tgl->format('Ymd');
                    } elseif ($setting->date_format === 'YYYY-MM') {
                        $datePart = $tgl->format('Y-m');
                    } elseif ($setting->date_format === 'YYYY/MM') {
                        $datePart = $tgl->format('Y/m');
                    } elseif ($setting->date_format === 'YYYY') {
                        $datePart = $tgl->format('Y');
                    }

                    $suffix = ($setting->suffix_type === 'RANDOM_ALPHANUMERIC_5') 
                        ? strtoupper(Str::random(5)) 
                        : ($setting->suffix_type === 'CUSTOM' ? ($setting->custom_suffix ?: '001') : str_pad($seq++, $padLen, '0', STR_PAD_LEFT));

                    $parts = array_filter([$prefix, $datePart, $suffix]);
                    $newCode = implode($sep, $parts);
                    $loan->update(['loan_code' => $newCode]);
                    $count++;
                }
            } elseif ($setting->entity === 'stock_bast') {
                $movements = StockMovement::where('reference_code', 'like', 'BAST%')->orderBy('id')->get();
                $seq = 1;
                foreach ($movements as $m) {
                    $prefix = $setting->prefix ?: 'BAST';
                    $datePart = '';
                    $tgl = $m->created_at ?? now();
                    if ($setting->date_format === 'YYYYMMDD') {
                        $datePart = $tgl->format('Ymd');
                    } elseif ($setting->date_format === 'YYYY-MM') {
                        $datePart = $tgl->format('Y-m');
                    } elseif ($setting->date_format === 'YYYY/MM') {
                        $datePart = $tgl->format('Y/m');
                    } elseif ($setting->date_format === 'YYYY') {
                        $datePart = $tgl->format('Y');
                    }

                    $suffix = ($setting->suffix_type === 'RANDOM_ALPHANUMERIC_5') 
                        ? strtoupper(Str::random(5)) 
                        : ($setting->suffix_type === 'CUSTOM' ? ($setting->custom_suffix ?: '001') : str_pad($seq++, $padLen, '0', STR_PAD_LEFT));

                    $parts = array_filter([$prefix, $datePart, $suffix]);
                    $newRef = implode($sep, $parts);
                    $m->update(['reference_code' => $newRef]);
                    $count++;
                }
            }

            return $count;
        });
    }
}
