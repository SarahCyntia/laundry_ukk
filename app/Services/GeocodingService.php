<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function getKecamatanFromAddress(string $alamat): ?string
    {
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'address' => $alamat . ', Surabaya',
                'key' => config('services.google_maps.key')
            ]
        );

        $components = $response['results'][0]['address_components'] ?? [];

        foreach ($components as $component) {
            if (in_array('administrative_area_level_3', $component['types'])) {
                return $component['long_name'];
            }
        }

        return null;
    }
}
