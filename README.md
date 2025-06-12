# Indonesian Area Data Package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zaidysf/idn-area.svg?style=flat-square)](https://packagist.org/packages/zaidysf/idn-area)
[![Support on Patreon](https://img.shields.io/badge/Support-Patreon-f96854?style=flat-square&logo=patreon)](https://patreon.com/zaidysf)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/zaidysf/idn-area/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/zaidysf/idn-area/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)

A comprehensive Laravel package providing complete Indonesian administrative area data including provinces, regencies, districts, villages, and islands. Fully compatible with Laravel 8 through Laravel 12 and PHP 8.0+.

## ✨ Features

- 🏛️ **Complete Administrative Hierarchy** - Province → Regency → District → Village
- 🏝️ **Islands Database** - Including outermost small islands and populated status
- 🔗 **Eloquent Models** - With proper relationships and type hints
- 🔍 **Search Functionality** - Across all area types with flexible queries
- ⚡ **Artisan Commands** - Easy data seeding and management
- 🚀 **Laravel 8-12 Support** - Full compatibility across all modern Laravel versions
- 🔐 **Foreign Key Constraints** - Ensuring data integrity
- 🧪 **Model Factories** - For comprehensive testing
- ⚙️ **Configurable** - Customizable through config file
- 📊 **PHPStan Level 6** - Strict type safety and static analysis

## 📋 Compatibility Matrix

| Laravel Version | PHP Version | Status |
|----------------|-------------|--------|
| Laravel 8.x | PHP 8.0, 8.1, 8.2 | ✅ Fully Supported |
| Laravel 9.x | PHP 8.0, 8.1, 8.2, 8.3 | ✅ Fully Supported |
| Laravel 10.x | PHP 8.1, 8.2, 8.3, 8.4 | ✅ Fully Supported |
| Laravel 11.x | PHP 8.2, 8.3, 8.4 | ✅ Fully Supported |
| Laravel 12.x | PHP 8.2, 8.3, 8.4 | ✅ Fully Supported |

## 🚀 Quick Start

### Installation

Install the package via Composer:

```bash
composer require zaidysf/idn-area
```

### Setup

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="idn-area-migrations"
php artisan migrate
```

### Seed Data

Populate your database with Indonesian area data:

```bash
php artisan idn-area:seed
```

### Start Using

```php
use zaidysf\IdnArea\Facades\IdnArea;

// Get all provinces
$provinces = IdnArea::provinces();

// Search for areas
$results = IdnArea::search('Jakarta');

// Get statistics
$stats = IdnArea::statistics();
```

## 📖 Detailed Installation

If you need more detailed setup options:

### Install Package

```bash
composer require zaidysf/idn-area
```

### Publish Migrations

```bash
php artisan vendor:publish --tag="idn-area-migrations"
php artisan migrate
```

### Optional: Publish Config

Optionally, you can publish the config file for customization:

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

## 📊 Data Overview

The package includes comprehensive Indonesian administrative data:

- 🏛️ **34 Provinces** - All Indonesian provinces
- 🏢 **514 Regencies/Cities** - Complete regency and city data  
- 🏘️ **7,230+ Districts** - All districts nationwide
- 🏡 **83,931 Villages** - Complete village database
- 🏝️ **17,508 Islands** - Including inhabited and outermost islands

### Data Quality

- ✅ **Government Sourced** - Official data from Indonesian government databases
- ✅ **Regularly Updated** - Maintained for accuracy and completeness
- ✅ **Validated Structure** - Foreign key constraints ensure data integrity
- ✅ **PHPStan Level 6** - Strict type safety and comprehensive static analysis

## 🧪 Testing

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run PHPStan analysis
composer analyse

# Fix code style
composer format

# Run specific test file
vendor/bin/pest tests/IdnAreaTest.php

# Run architecture tests
vendor/bin/pest tests/ArchTest.php
```

For detailed testing instructions, see [TESTING.md](TESTING.md).

## 📁 API Usage Example

For complete API controller examples, see [examples/IdnAreaController.php](examples/IdnAreaController.php).

```php
Route::prefix('api/idn-area')->group(function () {
    Route::get('provinces', [IdnAreaController::class, 'provinces']);
    Route::get('provinces/{provinceCode}/regencies', [IdnAreaController::class, 'regencies']);
    Route::get('regencies/{regencyCode}/districts', [IdnAreaController::class, 'districts']);
    Route::get('districts/{districtCode}/villages', [IdnAreaController::class, 'villages']);
    Route::get('search', [IdnAreaController::class, 'search']);
    Route::get('statistics', [IdnAreaController::class, 'statistics']);
});
```

## 📝 Performance Tips

### Optimize Queries

```php
// Use eager loading for relationships
$provinces = Province::with(['regencies.districts'])->get();

// Limit village searches for performance
$villages = Village::where('name', 'like', '%Jakarta%')->limit(100)->get();

// Use specific selects for large datasets
$regencies = Regency::select('code', 'name')->where('province_code', '32')->get();
```

### Caching Recommendations

```php
// Cache frequently accessed data
$provinces = Cache::remember('idn_provinces', 3600, function () {
    return Province::all();
});

// Cache search results
$searchResults = Cache::remember("search_{$query}", 1800, function () use ($query) {
    return IdnArea::search($query);
});
```

## 📅 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. Here's how you can contribute:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Make your changes** and add tests
4. **Run the test suite**: `composer test`
5. **Run static analysis**: `composer analyse`
6. **Fix code style**: `composer format`
7. **Commit your changes**: `git commit -m 'Add amazing feature'`
8. **Push to the branch**: `git push origin feature/amazing-feature`
9. **Open a Pull Request**

### Development Setup

```bash
# Clone the repository
git clone https://github.com/zaidysf/idn-area.git
cd idn-area

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse
```

## 🔒 Security Vulnerabilities

If you discover a security vulnerability within this package, please send an e-mail to Zaid Yasyaf via [zaid.ug@gmail.com](mailto:zaid.ug@gmail.com). All security vulnerabilities will be promptly addressed.

Please review [our security policy](../../security/policy) for more information on how to report security vulnerabilities.

## 📜 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🙏 Credits

- [Zaid Yasyaf](https://github.com/zaidysf) - Package Author
- [Indonesian Area Data](https://github.com/fityannugroho/idn-area-data) - Data source for Indonesian administrative areas
- [All Contributors](../../contributors) - Community contributors

---

<div align="center">

**Made with ❤️ in Indonesia**

[View on Packagist](https://packagist.org/packages/zaidysf/idn-area) • [Report Issues](https://github.com/zaidysf/idn-area/issues) • [Documentation](https://github.com/zaidysf/idn-area)

</div>
