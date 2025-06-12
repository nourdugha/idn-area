<?php

/**
 * Example usage of the Indonesian Area Data Package
 *
 * This file demonstrates various ways to use the package
 * in your Laravel application.
 */

use zaidysf\IdnArea\Facades\IdnArea;
use zaidysf\IdnArea\Models\District;
use zaidysf\IdnArea\Models\Island;
use zaidysf\IdnArea\Models\Province;
use zaidysf\IdnArea\Models\Regency;
use zaidysf\IdnArea\Models\Village;

/**
 * Example 1: Basic Usage with Facade
 */
function basicUsageExample()
{
    // Get all provinces
    $provinces = IdnArea::provinces();
    echo 'Total provinces: '.$provinces->count()."\n";

    // Get specific province (West Java)
    $westJava = IdnArea::province('32');
    if ($westJava) {
        echo 'Province: '.$westJava->name."\n";
    }

    // Get regencies in West Java
    $regencies = IdnArea::regenciesByProvince('32');
    echo 'Regencies in West Java: '.$regencies->count()."\n";

    // Get districts in Bandung regency
    $districts = IdnArea::districtsByRegency('32.04');
    echo 'Districts in Bandung: '.$districts->count()."\n";

    // Search for areas containing "Jakarta"
    $results = IdnArea::search('Jakarta');
    echo "Search results for 'Jakarta':\n";
    foreach ($results as $type => $items) {
        echo "- {$type}: ".$items->count()." items\n";
    }

    // Get area statistics
    $stats = IdnArea::statistics();
    echo "Area Statistics:\n";
    foreach ($stats as $key => $value) {
        echo "- {$key}: {$value}\n";
    }
}

/**
 * Example 2: Using Eloquent Models Directly
 */
function eloquentModelsExample()
{
    // Get province with all relationships
    $province = Province::with([
        'regencies.districts.villages',
    ])->find('32');

    if ($province) {
        echo 'Province: '.$province->name."\n";
        echo 'Total regencies: '.$province->regencies->count()."\n";

        foreach ($province->regencies->take(3) as $regency) {
            echo '- Regency: '.$regency->name.' (Districts: '.$regency->districts->count().")\n";
        }
    }

    // Find regencies with specific pattern
    $regencies = Regency::where('name', 'like', '%KOTA%')->get();
    echo 'Cities (KOTA): '.$regencies->count()."\n";

    // Get populated islands
    $populatedIslands = Island::populated()->get();
    echo 'Populated islands: '.$populatedIslands->count()."\n";

    // Get outermost small islands
    $outermostIslands = Island::outermostSmall()->get();
    echo 'Outermost small islands: '.$outermostIslands->count()."\n";
}

/**
 * Example 3: Building a Location Selector
 */
function locationSelectorExample()
{
    echo "=== Location Selector Example ===\n";

    // Step 1: Show all provinces for selection
    $provinces = Province::orderBy('name')->get();
    echo "Available Provinces:\n";
    foreach ($provinces->take(5) as $province) {
        echo "- [{$province->code}] {$province->name}\n";
    }

    // Step 2: User selects West Java (32)
    $selectedProvinceCode = '32';
    $regencies = Regency::where('province_code', $selectedProvinceCode)
        ->orderBy('name')
        ->get();

    echo "\nRegencies in West Java:\n";
    foreach ($regencies->take(5) as $regency) {
        echo "- [{$regency->code}] {$regency->name}\n";
    }

    // Step 3: User selects Bandung (32.04)
    $selectedRegencyCode = '32.04';
    $districts = District::where('regency_code', $selectedRegencyCode)
        ->orderBy('name')
        ->get();

    echo "\nDistricts in Bandung:\n";
    foreach ($districts->take(5) as $district) {
        echo "- [{$district->code}] {$district->name}\n";
    }

    // Step 4: User selects a district and gets villages
    $selectedDistrictCode = '32.04.01';
    $villages = Village::where('district_code', $selectedDistrictCode)
        ->orderBy('name')
        ->get();

    echo "\nVillages in selected district:\n";
    foreach ($villages->take(5) as $village) {
        echo "- [{$village->code}] {$village->name}\n";
    }
}

/**
 * Example 4: Search and Filter Functionality
 */
function searchAndFilterExample()
{
    echo "=== Search and Filter Example ===\n";

    // Search for specific terms
    $searchTerm = 'Sukabumi';
    $results = IdnArea::search($searchTerm);

    echo "Search results for '{$searchTerm}':\n";

    if (! empty($results['regencies']) && $results['regencies']->count() > 0) {
        echo "Regencies:\n";
        foreach ($results['regencies'] as $regency) {
            echo "- {$regency->name} ({$regency->province->name})\n";
        }
    }

    if (! empty($results['districts']) && $results['districts']->count() > 0) {
        echo "Districts:\n";
        foreach ($results['districts']->take(3) as $district) {
            echo "- {$district->name} ({$district->regency->name})\n";
        }
    }

    // Filter islands by type
    echo "\nIsland Examples:\n";

    $smallOutermostIslands = Island::outermostSmall()->take(5)->get();
    echo "Outermost Small Islands:\n";
    foreach ($smallOutermostIslands as $island) {
        echo "- {$island->name}\n";
    }

    $populatedIslands = Island::populated()->take(5)->get();
    echo "Populated Islands:\n";
    foreach ($populatedIslands as $island) {
        echo "- {$island->name}\n";
    }
}

/**
 * Example 5: Building Address Components
 */
function addressComponentsExample()
{
    echo "=== Address Components Example ===\n";

    // Get a complete address hierarchy for a village
    $villageCode = '32.04.01.2001'; // Example village code
    $village = Village::with([
        'district.regency.province',
    ])->find($villageCode);

    if ($village) {
        echo "Complete Address:\n";
        echo "Village: {$village->name}\n";
        echo "District: {$village->district->name}\n";
        echo "Regency: {$village->district->regency->name}\n";
        echo "Province: {$village->district->regency->province->name}\n";

        // Format as complete address
        $fullAddress = implode(', ', [
            $village->name,
            $village->district->name,
            $village->district->regency->name,
            $village->district->regency->province->name,
        ]);

        echo "Full Address: {$fullAddress}\n";
    }
}

/**
 * Example 6: API Response Helper
 */
function apiResponseExample()
{
    echo "=== API Response Example ===\n";

    // Format data for API response
    $provinces = Province::select('code', 'name')->get()->map(function ($province) {
        return [
            'value' => $province->code,
            'label' => $province->name,
        ];
    });

    echo "Province options for frontend:\n";
    echo json_encode($provinces->take(3)->toArray(), JSON_PRETTY_PRINT)."\n";

    // Hierarchical data structure
    $hierarchicalData = Province::with(['regencies' => function ($query) {
        $query->select('code', 'province_code', 'name')->limit(2);
    }])->select('code', 'name')->take(2)->get()->map(function ($province) {
        return [
            'code' => $province->code,
            'name' => $province->name,
            'regencies' => $province->regencies->map(function ($regency) {
                return [
                    'code' => $regency->code,
                    'name' => $regency->name,
                ];
            }),
        ];
    });

    echo "Hierarchical data:\n";
    echo json_encode($hierarchicalData->toArray(), JSON_PRETTY_PRINT)."\n";
}

// Run examples (uncomment to execute)
// basicUsageExample();
// eloquentModelsExample();
// locationSelectorExample();
// searchAndFilterExample();
// addressComponentsExample();
// apiResponseExample();
