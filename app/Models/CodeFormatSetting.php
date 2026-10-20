<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CodeFormatSetting extends Model
{
    protected $fillable = [
        'entity',
        'name',
        'prefix',
        'date_format',
        'suffix_type',
        'custom_suffix',
        'separator',
        'description',
    ];

    /**
     * Generate contoh preview string berdasarkan setting
     */
    public function generatePreview(?string $dynamicPrefix = null): string
    {
        $prefix = $dynamicPrefix ?? $this->prefix ?? 'KODE';
        $sep = $this->separator ?: '-';
        $datePart = '';

        if (!empty($this->date_format)) {
            $format = strtoupper($this->date_format);
            if ($format === 'YYYYMMDD') {
                $datePart = now()->format('Ymd');
            } elseif ($format === 'YYYY/MM') {
                $datePart = now()->format('Y/m');
            } elseif ($format === 'YYYY-MM') {
                $datePart = now()->format('Y-m');
            } elseif ($format === 'YYYY') {
                $datePart = now()->format('Y');
            } else {
                $datePart = now()->format('Ymd');
            }
        }

        $suffixPart = '001';
        switch ($this->suffix_type) {
            case 'INCREMENT_4_DIGIT':
                $suffixPart = '0001';
                break;
            case 'RANDOM_ALPHANUMERIC_5':
                $suffixPart = 'RRBQA';
                break;
            case 'CUSTOM':
                $suffixPart = $this->custom_suffix ?: '001';
                break;
            case 'INCREMENT_3_DIGIT':
            default:
                $suffixPart = '001';
                break;
        }

        $parts = array_filter([$prefix, $datePart, $suffixPart]);
        return implode($sep, $parts);
    }
}
