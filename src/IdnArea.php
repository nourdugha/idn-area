<?php

declare(strict_types=1);

namespace zaidysf\IdnArea;

use Illuminate\Database\Eloquent\Collection;
use zaidysf\IdnArea\Models\District;
use zaidysf\IdnArea\Models\Island;
use zaidysf\IdnArea\Models\Province;
use zaidysf\IdnArea\Models\Regency;
use zaidysf\IdnArea\Models\Village;

class IdnArea
{
    /**
     * Get all provinces.
     */
    public function provinces(): Collection
    {
        return Province::orderBy('name')->get();
    }

    /**
     * Get province by code.
     */
    public function province(string $code): ?Province
    {
        return Province::find($code);
    }

    /**
     * Get regencies by province code.
     */
    public function regenciesByProvince(string $provinceCode): Collection
    {
        return Regency::where('province_code', $provinceCode)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get regency by code.
     */
    public function regency(string $code): ?Regency
    {
        return Regency::find($code);
    }

    /**
     * Get districts by regency code.
     */
    public function districtsByRegency(string $regencyCode): Collection
    {
        return District::where('regency_code', $regencyCode)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get district by code.
     */
    public function district(string $code): ?District
    {
        return District::find($code);
    }

    /**
     * Get villages by district code.
     */
    public function villagesByDistrict(string $districtCode): Collection
    {
        return Village::where('district_code', $districtCode)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get village by code.
     */
    public function village(string $code): ?Village
    {
        return Village::find($code);
    }

    /**
     * Get islands by regency code.
     */
    public function islandsByRegency(string $regencyCode): Collection
    {
        return Island::where('regency_code', $regencyCode)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all outermost small islands.
     */
    public function outermostSmallIslands(): Collection
    {
        return Island::outermostSmall()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all populated islands.
     */
    public function populatedIslands(): Collection
    {
        return Island::populated()
            ->orderBy('name')
            ->get();
    }

    /**
     * Search areas by name.
     */
    public function search(string $query, string $type = 'all'): array
    {
        $results = [];

        if ($type === 'all' || $type === 'provinces') {
            $results['provinces'] = Province::where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->get();
        }

        if ($type === 'all' || $type === 'regencies') {
            $results['regencies'] = Regency::where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->get();
        }

        if ($type === 'all' || $type === 'districts') {
            $results['districts'] = District::where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->get();
        }

        if ($type === 'all' || $type === 'villages') {
            $results['villages'] = Village::where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(100) // Limit villages due to potentially large results
                ->get();
        }

        if ($type === 'all' || $type === 'islands') {
            $results['islands'] = Island::where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->get();
        }

        return $results;
    }

    /**
     * Get hierarchy: province -> regencies -> districts -> villages.
     */
    public function hierarchy(string $provinceCode): array
    {
        $province = Province::with([
            'regencies.districts.villages',
        ])->find($provinceCode);

        if (! $province) {
            return [];
        }

        return $province->toArray();
    }

    /**
     * Get area statistics.
     */
    public function statistics(): array
    {
        return [
            'provinces' => Province::count(),
            'regencies' => Regency::count(),
            'districts' => District::count(),
            'villages' => Village::count(),
            'islands' => Island::count(),
            'outermost_small_islands' => Island::outermostSmall()->count(),
            'populated_islands' => Island::populated()->count(),
        ];
    }
}
