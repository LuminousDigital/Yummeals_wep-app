<?php

namespace App\Services;

use App\Enums\Status;
use Exception;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BranchRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use Smartisan\Settings\Facades\Settings;

class BranchService
{
    protected array $branchFilter = [
        'name',
        'email',
        'phone',
        'latitude',
        'longitude',
        'city',
        'state',
        'zip_code',
        'address',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Branch::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->branchFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(BranchRequest $request)
    {
        try {
            return Branch::create($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(BranchRequest $request, Branch $branch)
    {
        try {
            return tap($branch)->update($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Branch $branch): void
    {
        try {
            if (Settings::group('site')->get("site_default_branch") != $branch->id) {
                $branch->delete();
            } else {
                throw new Exception("Default branch not deletable", 422);
            }
        } catch (Exception $exception) {
            Log::info(QueryExceptionLibrary::message($exception));
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Branch $branch): Branch
    {
        try {
            return $branch;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    public function updateZone(Request $request, Branch $branch): Branch
    {
        try {
            $coordinates = $request->zone;
            $branch->zone = json_encode($coordinates);
            $branch->update();
            return $branch;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    // public function showByLatLong(Request $request)
    // {
    //     try {
    //         $branches = Branch::whereNotNull('zone')->where('status', Status::ACTIVE)->get();
    //         $userLatitude = $request->input('latitude');
    //         $userLongitude = $request->input('longitude');

    //         foreach ($branches as $branch) {
    //             $zoneData = json_decode(json_decode($branch->zone, true), true);

    //             if ($this->isPointInPolygon($zoneData, $userLatitude, $userLongitude)) {
    //                 return $branch;
    //             }
                
    //         }

    //         throw new Exception(trans('all.message.out_of_service_area'), 422);
    //     } catch (Exception $exception) {
    //         Log::info($exception->getMessage());
    //         throw new Exception($exception->getMessage(), 422);
    //     }
    // }

    public function showByLatLong(Request $request)
{
    try {
        Log::debug('showByLatLong - Starting method', [
            'user_latitude' => $request->input('latitude'),
            'user_longitude' => $request->input('longitude'),
            'request_all' => $request->all()
        ]);

        $branches = Branch::whereNotNull('zone')->where('status', Status::ACTIVE)->get();
        
        Log::debug('showByLatLong - Branches found', [
            'total_branches' => $branches->count(),
            'branch_ids' => $branches->pluck('id')->toArray()
        ]);

        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');

        // Validate coordinates
        if (empty($userLatitude) || empty($userLongitude)) {
            Log::warning('showByLatLong - Missing coordinates', [
                'latitude' => $userLatitude,
                'longitude' => $userLongitude
            ]);
            throw new Exception(trans('all.message.invalid_coordinates'), 422);
        }

        Log::debug('showByLatLong - User coordinates', [
            'latitude' => $userLatitude,
            'longitude' => $userLongitude
        ]);

        $branchCounter = 0;
        foreach ($branches as $branch) {
            $branchCounter++;
            Log::debug("showByLatLong - Processing branch {$branchCounter}/{$branches->count()}", [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name
            ]);

            $zoneData = json_decode(json_decode($branch->zone, true), true);
            
            Log::debug("showByLatLong - Branch zone data", [
                'branch_id' => $branch->id,
                'zone_raw' => $branch->zone,
                'zone_decoded' => $zoneData,
                'zone_type' => gettype($zoneData)
            ]);

            // Check if zone data is valid
            if (empty($zoneData) || !is_array($zoneData)) {
                Log::warning("showByLatLong - Invalid zone data for branch", [
                    'branch_id' => $branch->id,
                    'zone_data' => $zoneData
                ]);
                continue;
            }

            Log::debug("showByLatLong - Calling isPointInPolygon", [
                'branch_id' => $branch->id,
                'zone_point_count' => count($zoneData),
                'user_lat' => $userLatitude,
                'user_lng' => $userLongitude
            ]);

            $isInPolygon = $this->isPointInPolygon($zoneData, $userLatitude, $userLongitude);
            
            Log::debug("showByLatLong - Polygon check result", [
                'branch_id' => $branch->id,
                'is_in_polygon' => $isInPolygon,
                'zone_points_sample' => array_slice($zoneData, 0, 3) // Log first 3 points for sample
            ]);

            if ($isInPolygon) {
                Log::info('showByLatLong - Branch found for coordinates', [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'user_latitude' => $userLatitude,
                    'user_longitude' => $userLongitude,
                    'branches_checked' => $branchCounter
                ]);
                return $branch;
            }
            return $branch;
        }

        Log::warning('showByLatLong - No branch found for coordinates', [
            'user_latitude' => $userLatitude,
            'user_longitude' => $userLongitude,
            'total_branches_checked' => $branches->count(),
            'branches_with_zones' => $branches->whereNotNull('zone')->count()
        ]);

        throw new Exception(trans('all.message.out_of_service_area'), 422);
    } catch (Exception $exception) {
        Log::error('showByLatLong - Exception occurred', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'user_latitude' => $request->input('latitude'),
            'user_longitude' => $request->input('longitude'),
            'trace' => $exception->getTraceAsString()
        ]);
        throw new Exception($exception->getMessage(), 422);
    }
}

    function isPointInPolygon($polygon, $latitude, $longitude)
    {
        $intersections = 0;
        $verticesCount = count($polygon);

        for ($i = 1; $i < $verticesCount; $i++) {
            $vertex1 = $polygon[$i - 1];
            $vertex2 = $polygon[$i];

            if ($vertex1['lng'] == $vertex2['lng'] && $longitude == $vertex1['lng']) {
                return true;
            }

            if (
                $longitude > min($vertex1['lng'], $vertex2['lng']) &&
                $longitude <= max($vertex1['lng'], $vertex2['lng']) &&
                $latitude <= max($vertex1['lat'], $vertex2['lat']) &&
                $vertex1['lng'] != $vertex2['lng']
            ) {
                $xinters = ($longitude - $vertex1['lng']) * ($vertex2['lat'] - $vertex1['lat']) / ($vertex2['lng'] - $vertex1['lng']) + $vertex1['lat'];

                if ($vertex1['lat'] == $vertex2['lat'] || $latitude <= $xinters) {
                    $intersections++;
                }
            }
        }
        return $intersections % 2 != 0;
    }
}