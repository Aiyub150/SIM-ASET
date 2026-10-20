<?php

namespace App\Http\Controllers;

use App\Models\CodeFormatSetting;
use App\Services\ActivityLogger;
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

        $setting->update([
            'prefix'        => $validated['prefix'] ? trim($validated['prefix']) : null,
            'date_format'   => $dateFormat,
            'suffix_type'   => $validated['suffix_type'],
            'custom_suffix' => $validated['suffix_type'] === 'CUSTOM' ? trim($validated['custom_suffix']) : null,
            'separator'     => $validated['separator'],
        ]);

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Format Kode',
            "Mengubah konfigurasi pola kode untuk: {$setting->name}",
            [
                'entity'  => $setting->entity,
                'preview' => $setting->generatePreview(),
            ],
            $request
        );

        return back()->with('success', "Konfigurasi format untuk \"{$setting->name}\" berhasil disimpan.");
    }
}
