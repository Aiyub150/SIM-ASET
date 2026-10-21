<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingController extends Controller
{
    /**
     * Forward geocoding: cari lokasi berdasarkan nama tempat / kata kunci
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));
        if (empty($query)) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $cacheKey = 'geocoding_search_' . md5(mb_strtolower($query));

        try {
            $data = Cache::remember($cacheKey, 86400, function () use ($query) {
                $response = Http::withHeaders([
                    'User-Agent'      => 'SIM-ASET-App/1.2 (aset-support@simaset.com)',
                    'Accept-Language' => 'id,en;q=0.8',
                ])->timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                    'format'        => 'jsonv2',
                    'q'             => $query,
                    'limit'         => 5,
                    'countrycodes'  => 'id',
                    'addressdetails'=> 1,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                // Fallback jika dibatasi countrycodes
                $fallback = Http::withHeaders([
                    'User-Agent'      => 'SIM-ASET-App/1.2 (aset-support@simaset.com)',
                    'Accept-Language' => 'id,en;q=0.8',
                ])->timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                    'format'        => 'jsonv2',
                    'q'             => $query,
                    'limit'         => 3,
                    'addressdetails'=> 1,
                ]);

                return $fallback->successful() ? $fallback->json() : [];
            });

            return response()->json([
                'success' => true,
                'data'    => $data ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::warning("Geocoding search failed for '{$query}': " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi layanan peta OpenStreetMap.',
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Reverse geocoding: ambil alamat nama tempat berdasarkan titik koordinat (lat, lng)
     */
    public function reverse(Request $request): JsonResponse
    {
        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        if (empty($lat) || empty($lng)) {
            return response()->json(['success' => false, 'message' => 'Koordinat tidak valid'], 422);
        }

        // Cache 7 hari untuk koordinat yang sama (dibulatkan ke 4 desimal ~11 meter)
        $roundedLat = round($lat, 4);
        $roundedLng = round($lng, 4);
        $cacheKey = "geocoding_reverse_{$roundedLat}_{$roundedLng}";

        try {
            $data = Cache::remember($cacheKey, 604800, function () use ($lat, $lng) {
                $response = Http::withHeaders([
                    'User-Agent'      => 'SIM-ASET-App/1.2 (aset-support@simaset.com)',
                    'Accept-Language' => 'id,en;q=0.8',
                ])->timeout(6)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format'        => 'jsonv2',
                    'lat'           => $lat,
                    'lon'           => $lng,
                    'addressdetails'=> 1,
                ]);

                return $response->successful() ? $response->json() : null;
            });

            if ($data && isset($data['display_name'])) {
                return response()->json([
                    'success'      => true,
                    'display_name' => $data['display_name'],
                    'address'      => $data['address'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Detail alamat tidak ditemukan untuk koordinat ini.',
            ]);
        } catch (\Throwable $e) {
            Log::warning("Geocoding reverse failed for ({$lat}, {$lng}): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi layanan geocoding.',
            ], 500);
        }
    }
}
