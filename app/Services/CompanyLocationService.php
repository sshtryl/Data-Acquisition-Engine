<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CompanyLocationService
{
    public function search(string $query): array
    {
        $userAgent = env('NOMINATIM_USER_AGENT', config('app.name', 'Laravel') . ' (' . config('app.url', env('APP_URL', 'http://localhost')) . ')');

        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => $userAgent,
                'Referer' => config('app.url', env('APP_URL', 'http://localhost')),
            ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'jsonv2',
                'addressdetails' => 1,
            ]);

        if ($response->status() === 403) {
            throw new \Exception("Nominatim menolak permintaan. Pastikan User-Agent memiliki kontak atau URL yang valid sesuai kebijakan Nominatim.");
        }

        if ($response->failed()) {
            throw new \Exception("Gagal menghubungi Nominatim API.");
        }

        $results = $response->json();

        if (empty($results)) {
            throw new \Exception("Lokasi tidak ditemukan untuk: {$query}");
        }

        $first = $results[0];

        return [
            'display_name' => $first['display_name'] ?? null,
            'latitude' => $first['lat'] ?? null,
            'longitude' => $first['lon'] ?? null,
            'importance' => $first['importance'] ?? null,
            'osm_type' => $first['osm_type'] ?? null,
            'address' => $first['address'] ?? [],
        ];
    }
}