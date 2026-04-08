<?php

namespace App\Http\Controllers;

use App\Services\ReverseGeocodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    public function reverse(Request $request, ReverseGeocodeService $geocode): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $payload = $geocode->lookup(
            (float) $validated['lat'],
            (float) $validated['lng']
        );

        return response()->json($payload);
    }
}
