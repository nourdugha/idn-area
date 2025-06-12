# Changelog

All notable changes to `idn-area` will be documented in this file.

## [Unreleased]

## [1.0.0] - 2025-06-12

### Added
- Initial release of Indonesian Area Data package for Laravel
- Complete administrative hierarchy support:
  - 38 Provinces with official government codes
  - 500+ Regencies and Cities 
  - 7,000+ Districts (Kecamatan)
  - 80,000+ Villages (Kelurahan/Desa)
  - 17,000+ Islands with populated and outermost flags
- Laravel framework compatibility:
  - Laravel 8.x, 9.x, 10.x, 11.x, 12.x support
  - PHP 8.0, 8.1, 8.2, 8.3, 8.4 compatibility
- Eloquent models with proper relationships:
  - Province, Regency, District, Village, Island models
  - HasFactory trait integration for testing
  - Foreign key constraints for data integrity
  - Eloquent scopes for island filtering (populated, outermost)
- Data management features:
  - Artisan command for data seeding (`php artisan idn-area:seed`)
  - Force reseed option with `--force` flag
  - Memory-efficient chunked CSV processing
  - Transaction-safe database operations
- Search and query capabilities:
  - Search across all area types
  - Hierarchical data retrieval
  - Area statistics and metrics
  - Configurable search limits
- Developer experience:
  - Laravel Facade support (`IdnArea::provinces()`)
  - Service provider with auto-discovery
  - Comprehensive configuration options
  - Model factories for testing with specific states
  - Complete API controller examples
- Testing infrastructure:
  - 94+ comprehensive test cases
  - Model relationship testing
  - Factory testing with states
  - Architecture compliance testing
  - Seeder service testing
  - Command testing
  - Facade integration testing
- Quality assurance:
  - PHPStan level 6 static analysis
  - Laravel Pint code styling
  - GitHub Actions CI/CD pipeline
  - Multi-version compatibility testing
  - Dependabot dependency management
- Documentation:
  - Comprehensive README with examples
  - API controller templates
  - Usage examples and patterns
  - Installation and configuration guides
  - Testing documentation

### Technical Details
- Package built using Spatie Laravel Package Tools
- PSR-4 autoloading with proper namespace structure
- Strict type declarations for better code quality
- Database migrations with proper indexing
- Configurable table prefixes and model overrides
- Memory-efficient data processing for large datasets

### Data Sources
- Official Indonesian government administrative data
- Credit to [idn-area-data](https://github.com/fityannugroho/idn-area-data) for data source
- Regular updates following official government changes

[Unreleased]: https://github.com/zaidysf/idn-area/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/zaidysf/idn-area/releases/tag/v1.0.0
