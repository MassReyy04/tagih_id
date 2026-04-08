<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReverseGeocodeService
{
    /**
     * Reverse geocoding via Nominatim (OSM). Harus dipanggil dari server — policy membutuhkan User-Agent valid.
     *
     * @return array{geo_jalan: string, geo_kelurahan: string, geo_kecamatan: string, geo_kota: string, geo_provinsi: string, geo_kode_pos: string}
     */
    public function lookup(float $lat, float $lng): array
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        $response = Http::withHeaders([
            'User-Agent' => 'TagihMitra/1.0 ('.$host.')',
            'Accept' => 'application/json',
        ])->timeout(20)->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'jsonv2',
            'lat' => $lat,
            'lon' => $lng,
            'accept-language' => 'id',
        ]);

        if (! $response->successful()) {
            return $this->emptyFields();
        }

        $address = $response->json('address');
        $mapped = is_array($address) ? $this->mapNominatimAddress($address) : $this->emptyFields();

        if (trim($mapped['geo_kecamatan']) === '') {
            $mapped = $this->enrichKecamatanFromBigDataCloud($lat, $lng, $mapped);
        }

        return $mapped;
    }

    /**
     * @param  array<string, mixed>  $a
     * @return array{geo_jalan: string, geo_kelurahan: string, geo_kecamatan: string, geo_kota: string, geo_provinsi: string, geo_kode_pos: string}
     */
    public function mapNominatimAddress(array $a): array
    {
        $road = $a['road'] ?? $a['pedestrian'] ?? $a['footway'] ?? $a['path'] ?? '';
        $jalan = trim(implode(' ', array_filter([
            is_string($road) ? $road : '',
            isset($a['house_number']) && is_string($a['house_number']) ? $a['house_number'] : '',
        ])));

        if ($jalan === '' && ! empty($a['neighbourhood']) && is_string($a['neighbourhood'])) {
            $jalan = $a['neighbourhood'];
        }

        $kelurahan = '';
        foreach (['suburb', 'village', 'neighbourhood'] as $k) {
            if (! empty($a[$k]) && is_string($a[$k])) {
                $kelurahan = $a[$k];
                break;
            }
        }

        $kecamatan = '';
        foreach (['city_district', 'district', 'county'] as $k) {
            if (! empty($a[$k]) && is_string($a[$k])) {
                $kecamatan = $a[$k];
                break;
            }
        }

        $kota = '';
        foreach (['city', 'town', 'municipality'] as $k) {
            if (! empty($a[$k]) && is_string($a[$k])) {
                $kota = $a[$k];
                break;
            }
        }
        if ($kota === '' && ! empty($a['county']) && is_string($a['county'])) {
            $kota = $a['county'];
        }

        $provinsi = '';
        foreach (['state', 'region'] as $k) {
            if (! empty($a[$k]) && is_string($a[$k])) {
                $provinsi = $a[$k];
                break;
            }
        }

        return [
            'geo_jalan' => $jalan,
            'geo_kelurahan' => $kelurahan,
            'geo_kecamatan' => $kecamatan,
            'geo_kota' => $kota,
            'geo_provinsi' => $provinsi,
            'geo_kode_pos' => isset($a['postcode']) && is_string($a['postcode']) ? $a['postcode'] : '',
        ];
    }

    /**
     * @return array{geo_jalan: string, geo_kelurahan: string, geo_kecamatan: string, geo_kota: string, geo_provinsi: string, geo_kode_pos: string}
     */
    private function emptyFields(): array
    {
        return [
            'geo_jalan' => '',
            'geo_kelurahan' => '',
            'geo_kecamatan' => '',
            'geo_kota' => '',
            'geo_provinsi' => '',
            'geo_kode_pos' => '',
        ];
    }

    /**
     * Nominatim sering tidak mengisi "district"/kecamatan untuk Indonesia. BigDataCloud
     * (reverse-geocode-client, tanpa API key) biasanya mengisi kecamatan di locality / informative.
     *
     * @param  array<string, string>  $base
     * @return array<string, string>
     */
    private function enrichKecamatanFromBigDataCloud(float $lat, float $lng, array $base): array
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'TagihMitra/1.0 ('.$host.')',
                'Accept' => 'application/json',
            ])->timeout(12)->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                'latitude' => $lat,
                'longitude' => $lng,
                'localityLanguage' => 'id',
            ]);

            if (! $response->successful()) {
                return $base;
            }

            $data = $response->json();
            if (! is_array($data)) {
                return $base;
            }

            $kecamatan = '';

            foreach ($data['localityInfo']['informative'] ?? [] as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $desc = strtolower((string) ($item['description'] ?? ''));
                if (str_contains($desc, 'kecamatan')) {
                    $name = trim((string) ($item['name'] ?? ''));
                    if ($name !== '') {
                        $kecamatan = $name;
                        break;
                    }
                }
            }

            if ($kecamatan === '') {
                $loc = trim((string) ($data['locality'] ?? ''));
                $kel = trim((string) ($base['geo_kelurahan'] ?? ''));
                $kota = trim((string) ($base['geo_kota'] ?? ''));

                if ($loc !== '' && strcasecmp($loc, $kel) !== 0 && strcasecmp($loc, $kota) !== 0) {
                    $kecamatan = $loc;
                }
            }

            if ($kecamatan !== '') {
                $base['geo_kecamatan'] = $kecamatan;
            }
        } catch (\Throwable) {
            // biarkan base apa adanya
        }

        return $base;
    }
}
