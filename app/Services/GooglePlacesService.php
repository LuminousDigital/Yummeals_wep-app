<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_places.key');
    }

public function getPlacePredictions(string $input, string $sessionToken): array
{

    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';

    $response = Http::withOptions([
        'verify' => false,
    ])->get($url, [
        'input' => $input,
        'key' => $this->apiKey,
        'sessiontoken' => $sessionToken,
        'components' => 'country:NG',
    ]);

    return $response->json();
}
public function getPlaceDetails(string $placeId, string $sessionToken): array
{
    $url = 'https://maps.googleapis.com/maps/api/place/details/json';

    $response = Http::withOptions([
        'verify' => false,
    ])->get($url, [
        'place_id' => $placeId,
        'fields' => 'formatted_address,geometry',
        'key' => $this->apiKey,
        'sessiontoken' => $sessionToken,
    ]);

    return $response->json();
}
}
