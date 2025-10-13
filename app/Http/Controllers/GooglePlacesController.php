<?php

namespace App\Http\Controllers;

use App\Services\GooglePlacesService;
use Illuminate\Http\Request;

class GooglePlacesController extends Controller
{
    protected GooglePlacesService $googlePlacesService;

    public function __construct(GooglePlacesService $googlePlacesService)
    {
        $this->googlePlacesService = $googlePlacesService;
    }

public function autocomplete(Request $request)
{

    $request->validate([
        'input' => 'required|string',
        'sessiontoken' => 'required|string',
    ]);

    $result = $this->googlePlacesService->getPlacePredictions(
        $request->input('input'),
        $request->input('sessiontoken')
    );

    return response()->json($result);
}

public function details(Request $request)
{
    $request->validate([
        'place_id' => 'required|string',
        'sessiontoken' => 'required|string',
    ]);

    $result = $this->googlePlacesService->getPlaceDetails(
        $request->input('place_id'),
        $request->input('sessiontoken')
    );

    return response()->json($result);
}

public function reverseGeocode(Request $request)
{
    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    $result = $this->googlePlacesService->reverseGeocode(
        $request->input('latitude'),
        $request->input('longitude')
    );

    return response()->json($result);
}
}
