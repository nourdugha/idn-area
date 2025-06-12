<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use zaidysf\IdnArea\Facades\IdnArea;
use zaidysf\IdnArea\Models\District;
use zaidysf\IdnArea\Models\Province;
use zaidysf\IdnArea\Models\Regency;
use zaidysf\IdnArea\Models\Village;

/**
 * Indonesian Area Data API Controller
 *
 * Example controller showing how to create API endpoints
 * for Indonesian administrative area data.
 */
class IdnAreaController extends Controller
{
    /**
     * Get all provinces.
     */
    public function provinces(): JsonResponse
    {
        $provinces = IdnArea::provinces()->map(function ($province) {
            return [
                'code' => $province->code,
                'name' => $province->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $provinces,
            'total' => $provinces->count(),
        ]);
    }

    /**
     * Get regencies by province code.
     */
    public function regencies(Request $request, string $provinceCode): JsonResponse
    {
        $province = IdnArea::province($provinceCode);

        if (! $province) {
            return response()->json([
                'success' => false,
                'message' => 'Province not found',
            ], 404);
        }

        $regencies = IdnArea::regenciesByProvince($provinceCode)->map(function ($regency) {
            return [
                'code' => $regency->code,
                'name' => $regency->name,
                'province_code' => $regency->province_code,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $regencies,
            'province' => [
                'code' => $province->code,
                'name' => $province->name,
            ],
            'total' => $regencies->count(),
        ]);
    }

    /**
     * Get districts by regency code.
     */
    public function districts(Request $request, string $regencyCode): JsonResponse
    {
        $regency = IdnArea::regency($regencyCode);

        if (! $regency) {
            return response()->json([
                'success' => false,
                'message' => 'Regency not found',
            ], 404);
        }

        $districts = IdnArea::districtsByRegency($regencyCode)->map(function ($district) {
            return [
                'code' => $district->code,
                'name' => $district->name,
                'regency_code' => $district->regency_code,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $districts,
            'regency' => [
                'code' => $regency->code,
                'name' => $regency->name,
            ],
            'total' => $districts->count(),
        ]);
    }

    /**
     * Get villages by district code.
     */
    public function villages(Request $request, string $districtCode): JsonResponse
    {
        $district = IdnArea::district($districtCode);

        if (! $district) {
            return response()->json([
                'success' => false,
                'message' => 'District not found',
            ], 404);
        }

        $villages = IdnArea::villagesByDistrict($districtCode)->map(function ($village) {
            return [
                'code' => $village->code,
                'name' => $village->name,
                'district_code' => $village->district_code,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $villages,
            'district' => [
                'code' => $district->code,
                'name' => $district->name,
            ],
            'total' => $villages->count(),
        ]);
    }

    /**
     * Search areas by name.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'sometimes|string|in:all,provinces,regencies,districts,villages,islands',
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $limit = $request->input('limit', 10);

        $results = IdnArea::search($query, $type);

        // Apply limit to each result type
        $formattedResults = [];
        foreach ($results as $resultType => $items) {
            $formattedResults[$resultType] = $items->take($limit)->map(function ($item) {
                return [
                    'code' => $item->code,
                    'name' => $item->name,
                ];
            })->values();
        }

        return response()->json([
            'success' => true,
            'data' => $formattedResults,
            'query' => $query,
            'type' => $type,
        ]);
    }

    /**
     * Get hierarchical data for a province.
     */
    public function hierarchy(Request $request, string $provinceCode): JsonResponse
    {
        $request->validate([
            'include_villages' => 'sometimes|boolean',
        ]);

        $includeVillages = $request->boolean('include_villages', false);

        if ($includeVillages) {
            $province = Province::with([
                'regencies.districts.villages',
            ])->find($provinceCode);
        } else {
            $province = Province::with([
                'regencies.districts',
            ])->find($provinceCode);
        }

        if (! $province) {
            return response()->json([
                'success' => false,
                'message' => 'Province not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $province,
        ]);
    }

    /**
     * Get area statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = IdnArea::statistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get all islands with filtering options.
     */
    public function islands(Request $request): JsonResponse
    {
        $request->validate([
            'regency_code' => 'sometimes|string|exists:idn_regencies,code',
            'populated' => 'sometimes|boolean',
            'outermost_small' => 'sometimes|boolean',
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = \zaidysf\IdnArea\Models\Island::query();

        if ($request->has('regency_code')) {
            $query->where('regency_code', $request->input('regency_code'));
        }

        if ($request->has('populated')) {
            $populated = $request->boolean('populated');
            $query->where('is_populated', $populated);
        }

        if ($request->has('outermost_small')) {
            $outermostSmall = $request->boolean('outermost_small');
            $query->where('is_outermost_small', $outermostSmall);
        }

        $limit = $request->input('limit', 50);
        $islands = $query->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function ($island) {
                return [
                    'id' => $island->id,
                    'code' => $island->code,
                    'name' => $island->name,
                    'coordinate' => $island->coordinate,
                    'is_populated' => $island->is_populated,
                    'is_outermost_small' => $island->is_outermost_small,
                    'regency_code' => $island->regency_code,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $islands,
            'total' => $islands->count(),
        ]);
    }

    /**
     * Get area details by code (auto-detect type).
     */
    public function details(Request $request, string $code): JsonResponse
    {
        // Try to determine the type by code length and pattern
        $codeLength = strlen($code);
        $area = null;
        $type = null;

        switch ($codeLength) {
            case 2:
                $area = Province::find($code);
                $type = 'province';
                break;
            case 5:
                $area = Regency::with('province')->find($code);
                $type = 'regency';
                break;
            case 8:
                $area = District::with('regency.province')->find($code);
                $type = 'district';
                break;
            case 13:
                $area = Village::with('district.regency.province')->find($code);
                $type = 'village';
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid area code format',
                ], 400);
        }

        if (! $area) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type).' not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $area,
            'type' => $type,
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Example Routes
|--------------------------------------------------------------------------
|
| Add these routes to your routes/api.php file:
|
| Route::prefix('idn-area')->group(function () {
|     Route::get('provinces', [IdnAreaController::class, 'provinces']);
|     Route::get('provinces/{provinceCode}/regencies', [IdnAreaController::class, 'regencies']);
|     Route::get('regencies/{regencyCode}/districts', [IdnAreaController::class, 'districts']);
|     Route::get('districts/{districtCode}/villages', [IdnAreaController::class, 'villages']);
|     Route::get('search', [IdnAreaController::class, 'search']);
|     Route::get('provinces/{provinceCode}/hierarchy', [IdnAreaController::class, 'hierarchy']);
|     Route::get('statistics', [IdnAreaController::class, 'statistics']);
|     Route::get('islands', [IdnAreaController::class, 'islands']);
|     Route::get('details/{code}', [IdnAreaController::class, 'details']);
| });
|
|--------------------------------------------------------------------------
| Example API Usage
|--------------------------------------------------------------------------
|
| GET /api/idn-area/provinces
| GET /api/idn-area/provinces/32/regencies
| GET /api/idn-area/regencies/32.04/districts
| GET /api/idn-area/districts/32.04.01/villages
| GET /api/idn-area/search?q=Jakarta&type=all&limit=10
| GET /api/idn-area/provinces/32/hierarchy?include_villages=true
| GET /api/idn-area/statistics
| GET /api/idn-area/islands?populated=true&limit=20
| GET /api/idn-area/details/32.04.01.2001
|
*/
