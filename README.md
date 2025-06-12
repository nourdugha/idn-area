# Indonesian Area Data Package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zaidysf/idn-area.svg?style=flat-square)](https://packagist.org/packages/zaidysf/idn-area)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/zaidysf/idn-area/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/zaidysf/idn-area/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/zaidysf/idn-area/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/zaidysf/idn-area/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/zaidysf/idn-area.svg?style=flat-square)](https://packagist.org/packages/zaidysf/idn-area)
[![License](https://img.shields.io/packagist/l/zaidysf/idn-area.svg?style=flat-square)](https://packagist.org/packages/zaidysf/idn-area)

A comprehensive Laravel package providing Indonesian administrative area data including provinces, regencies, districts, villages, and islands. This package is compatible with Laravel 8, 9, 10, 11, and 12.

## Features

- Complete Indonesian administrative hierarchy (Province → Regency → District → Village)
- Islands data including outermost small islands and populated status
- Eloquent models with proper relationships
- Search functionality across all area types
- Artisan command for data seeding
- Support for Laravel 8+ and PHP 8.0+
- Foreign key constraints for data integrity
- Model factories for testing
- Configurable through config file

## 📋 Compatibility Matrix

| Laravel Version | PHP Version | Status |
|----------------|-------------|---------|
| Laravel 8.x | PHP 8.0+ | ✅ Supported |
| Laravel 9.x | PHP 8.0+ | ✅ Supported |
| Laravel 10.x | PHP 8.1+ | ✅ Supported |
| Laravel 11.x | PHP 8.2+ | ✅ Supported |
| Laravel 12.x | PHP 8.2+ | ✅ Supported |

## Installation

You can install the package via Composer:

```bash
composer require zaidysf/idn-area
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="idn-area-migrations"
php artisan migrate
```

Optionally, you can publish the config file:

```bash
php artisan vendor:publish --tag="idn-area-config"
```

## Data Seeding

Seed the Indonesian area data using the provided Artisan command:

```bash
php artisan idn-area:seed
```

To force reseed (clear existing data and reseed):

```bash
php artisan idn-area:seed --force
```

## Usage

### Using the Facade

```php
use zaidysf\IdnArea\Facades\IdnArea;

// Get all provinces
$provinces = IdnArea::provinces();

// Get specific province
$province = IdnArea::province('32'); // West Java

// Get regencies by province
$regencies = IdnArea::regenciesByProvince('32');

// Get districts by regency
$districts = IdnArea::districtsByRegency('32.04'); // Bandung

// Get villages by district  
$villages = IdnArea::villagesByDistrict('32.04.01'); // Sukasari

// Get islands by regency
$islands = IdnArea::islandsByRegency('32.04');

// Search across all area types
$results = IdnArea::search('Jakarta');

// Get area statistics
$stats = IdnArea::statistics();
```

### Using Models Directly

```php
use zaidysf\IdnArea\Models\Province;
use zaidysf\IdnArea\Models\Regency;
use zaidysf\IdnArea\Models\District;
use zaidysf\IdnArea\Models\Village;
use zaidysf\IdnArea\Models\Island;

// Get province with relationships
$province = Province::with(['regencies.districts.villages'])->find('32');

// Get regency with province
$regency = Regency::with('province')->find('32.04');

// Search for villages
$villages = Village::where('name', 'like', '%Sukamaju%')->get();

// Get populated islands only
$populatedIslands = Island::populated()->get();

// Get outermost small islands
$outermostIslands = Island::outermostSmall()->get();
```

### Model Relationships

```php
// Province relationships
$province = Province::find('32');
$regencies = $province->regencies;
$districts = $province->districts;

// Regency relationships  
$regency = Regency::find('32.04');
$province = $regency->province;
$districts = $regency->districts;
$villages = $regency->villages;
$islands = $regency->islands;

// District relationships
$district = District::find('32.04.01');
$regency = $district->regency;
$province = $district->province();
$villages = $district->villages;

// Village relationships
$village = Village::find('32.04.01.2001');
$district = $village->district;
$regency = $village->regency();
$province = $village->province();

// Island relationships
$island = Island::find(1);
$regency = $island->regency;
$province = $island->province();
```

### Using Model Factories (for Testing)

```php
// Create test data using factories
$province = Province::factory()->jakarta()->create();
$regency = Regency::factory()->forProvince('32')->create();
$district = District::factory()->forRegency('32.04')->create();
$village = Village::factory()->forDistrict('32.04.01')->create();

// Create islands with specific attributes
$populatedIsland = Island::factory()->populated()->create();
$outermostIsland = Island::factory()->outermostSmall()->create();
```

## Data Structure

### Provinces
- `code`: 2-digit province code
- `name`: Province name

### Regencies  
- `code`: 5-character regency code (XX.YY format)
- `province_code`: Reference to province
- `name`: Regency name

### Districts
- `code`: 8-character district code (XX.YY.ZZ format)  
- `regency_code`: Reference to regency
- `name`: District name

### Villages
- `code`: 13-character village code (XX.YY.ZZ.AAAA format)
- `district_code`: Reference to district  
- `name`: Village name

### Islands
- `id`: Auto-increment ID
- `code`: Island code (optional)
- `coordinate`: Geographic coordinates (optional)
- `name`: Island name
- `is_outermost_small`: Boolean flag for outermost small islands
- `is_populated`: Boolean flag for populated status
- `regency_code`: Reference to regency (optional)

## Configuration

The config file allows you to customize various aspects:

```php
return [
    'table_prefix' => 'idn_',
    'enable_foreign_keys' => true,
    'search' => [
        'village_limit' => 100,
        'case_sensitive' => false,
    ],
    'models' => [
        'province' => \zaidysf\IdnArea\Models\Province::class,
        'regency' => \zaidysf\IdnArea\Models\Regency::class,
        'district' => \zaidysf\IdnArea\Models\District::class,
        'village' => \zaidysf\IdnArea\Models\Village::class,
        'island' => \zaidysf\IdnArea\Models\Island::class,
    ],
];
```

## Data Sources

The data is sourced from official Indonesian government databases and maintained for accuracy. The package includes:

- 38 Provinces  
- 500+ Regencies/Cities
- 7,000+ Districts
- 80,000+ Villages
- 17,000+ Islands

## Testing

```bash
# Run all tests
composer test

# Run tests with coverage
composer run test-coverage

# Run specific test file
vendor/bin/pest tests/IdnAreaTest.php

# Run architecture tests
vendor/bin/pest tests/ArchTest.php
```

For detailed testing instructions, see [TESTING.md](TESTING.md).

## API Usage Example

For complete API controller examples, see [examples/IdnAreaController.php](examples/IdnAreaController.php).

```php
Route::prefix('api/idn-area')->group(function () {
    Route::get('provinces', [IdnAreaController::class, 'provinces']);
    Route::get('provinces/{provinceCode}/regencies', [IdnAreaController::class, 'regencies']);
    Route::get('search', [IdnAreaController::class, 'search']);
    Route::get('statistics', [IdnAreaController::class, 'statistics']);
});
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Zaid Yasyaf](https://github.com/zaidysf)
- [Indonesian Area Data](https://github.com/fityannugroho/idn-area-data) - Data source for Indonesian administrative areas
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
