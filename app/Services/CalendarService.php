<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Mengambil data libur nasional bulan ini dari API.
     * Caching selama 1 hari (24 jam) untuk menghindari request berlebihan.
     */
    public function getHolidaysForCurrentMonth(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;
        
        // Cache key unik per bulan/tahun
        $cacheKey = "holidays_{$year}_{$month}";

        return Cache::remember($cacheKey, now()->addDay(), function () use ($year, $month) {
            try {
                $response = Http::timeout(5)->get("https://api.kemendesa.link/libur-nasional/api/holidays/{$year}.json");

                if ($response->successful()) {
                    $json = $response->json();
                    
                    if (!isset($json['data'])) {
                        return [];
                    }

                    // Filter hari libur hanya untuk bulan dan tahun berjalan
                    $holidays = collect($json['data'])->filter(function ($holiday) use ($year, $month) {
                        if (isset($holiday['date'])) {
                            $date = Carbon::parse($holiday['date']);
                            return $date->year === $year && $date->month === $month;
                        }
                        return false;
                    })->map(function ($holiday) {
                        return [
                            'date' => $holiday['date'],
                            'title' => $holiday['name'] ?? 'Hari Libur',
                            'is_cuti' => $holiday['is_cuti_bersama'] ?? false
                        ];
                    })->values()->toArray();

                    return $holidays;
                }
            } catch (\Exception $e) {
                // Return array kosong jika API gagal atau timeout
                return [];
            }
            
            return [];
        });
    }
}
