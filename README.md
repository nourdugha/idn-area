# IDN Area: Comprehensive Indonesian Administrative Area Data for Laravel

![IDN Area](https://img.shields.io/badge/IDN%20Area-Data%20Package-brightgreen)

Welcome to the **IDN Area** repository! This package provides a complete dataset of Indonesian administrative areas for Laravel versions 8 to 12. You can access the data for provinces, regencies, districts, villages, and islands. This repository aims to simplify the integration of geographic and government data into your Laravel applications.

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Data Structure](#data-structure)
- [Contributing](#contributing)
- [License](#license)
- [Releases](#releases)
- [Support](#support)

## Introduction

The IDN Area package is designed for developers who need reliable and structured data about Indonesia's administrative regions. This data can be crucial for applications that require geographic information, such as mapping services, government applications, and demographic studies. 

By utilizing this package, you can easily manage and query administrative areas, allowing you to focus on building your application rather than gathering data.

## Features

- **Comprehensive Dataset**: Access data for all provinces, regencies, districts, villages, and islands in Indonesia.
- **Laravel Integration**: Seamlessly integrate with Laravel 8 to 12.
- **Eloquent Support**: Utilize Laravel's Eloquent ORM for easy data manipulation.
- **PHP 8 Compatibility**: Built to work efficiently with PHP 8.
- **PHPStan Support**: Includes static analysis for better code quality.
- **Well-Documented**: Clear documentation for easy setup and usage.

## Installation

To install the IDN Area package, you can use Composer. Run the following command in your terminal:

```bash
composer require nourdugha/idn-area
```

Once the installation is complete, you can publish the configuration file with:

```bash
php artisan vendor:publish --provider="Nourdugha\IDNArea\IDNAreaServiceProvider"
```

This will allow you to customize the package settings according to your application's needs.

## Usage

After installation, you can start using the data in your Laravel application. Here’s a simple example to get you started:

### Fetching Provinces

You can fetch all provinces using Eloquent:

```php
use Nourdugha\IDNArea\Models\Province;

$provinces = Province::all();
```

### Fetching Districts

To get districts for a specific province:

```php
use Nourdugha\IDNArea\Models\District;

$districts = District::where('province_id', $provinceId)->get();
```

### Fetching Villages

For villages within a specific district:

```php
use Nourdugha\IDNArea\Models\Village;

$villages = Village::where('district_id', $districtId)->get();
```

## Data Structure

The IDN Area package organizes data into several models, each representing different administrative levels:

- **Province**: Represents the highest level of administrative division.
- **Regency**: Represents subdivisions within provinces.
- **District**: Represents subdivisions within regencies.
- **Village**: Represents the smallest administrative unit.
- **Island**: Represents geographic islands in Indonesia.

Each model has relationships defined to facilitate easy data retrieval.

### Example Relationships

```php
class Province extends Model {
    public function regencies() {
        return $this->hasMany(Regency::class);
    }
}

class Regency extends Model {
    public function districts() {
        return $this->hasMany(District::class);
    }
}
```

## Contributing

We welcome contributions to improve the IDN Area package. If you want to contribute, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them.
4. Push your branch to your forked repository.
5. Open a pull request.

Please ensure that your code follows the project's coding standards and is well-documented.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Releases

For the latest updates and versions of the IDN Area package, visit the [Releases](https://github.com/nourdugha/idn-area/releases) section. Here, you can find downloadable files that you need to execute in your projects.

## Support

If you have any questions or need assistance, feel free to open an issue in the repository or contact the maintainers. We aim to respond promptly to all inquiries.

## Conclusion

The IDN Area package is a valuable resource for developers working with Indonesian administrative data. By integrating this package into your Laravel application, you can streamline data management and focus on building features that matter.

For more information, visit the [Releases](https://github.com/nourdugha/idn-area/releases) section for the latest updates.