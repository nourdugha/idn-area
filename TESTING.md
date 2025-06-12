# Testing Guide

## Running All Tests

To run all test files in the package:

```bash
# Run all tests
vendor/bin/pest

# Run tests with coverage
vendor/bin/pest --coverage

# Run tests in verbose mode
vendor/bin/pest --verbose

# Run specific test file
vendor/bin/pest tests/IdnAreaTest.php
vendor/bin/pest tests/RelationshipTest.php
vendor/bin/pest tests/SeederTest.php
vendor/bin/pest tests/CommandTest.php
vendor/bin/pest tests/FacadeTest.php
vendor/bin/pest tests/ExampleTest.php
vendor/bin/pest tests/ArchTest.php
vendor/bin/pest tests/FactoryTest.php

# Run tests with parallel execution
vendor/bin/pest --parallel

# Run tests with specific filter
vendor/bin/pest --filter="can create province"
```

## Running Individual Test Suites

```bash
# Test core functionality
vendor/bin/pest tests/IdnAreaTest.php --verbose

# Test model relationships
vendor/bin/pest tests/RelationshipTest.php --verbose

# Test model factories
vendor/bin/pest tests/FactoryTest.php --verbose

# Test architecture compliance
vendor/bin/pest tests/ArchTest.php --verbose

# Test facade integration
vendor/bin/pest tests/FacadeTest.php --verbose
```

## Running Tests with Different Outputs

```bash
# JSON output
vendor/bin/pest --format=json

# JUnit XML output for CI
vendor/bin/pest --log-junit=build/report.junit.xml

# Generate coverage report
vendor/bin/pest --coverage --coverage-html=build/coverage
```

## Docker Testing (Optional)

```bash
# Test with different PHP versions
docker run --rm -v $(pwd):/app -w /app php:8.0-cli composer test
docker run --rm -v $(pwd):/app -w /app php:8.1-cli composer test
docker run --rm -v $(pwd):/app -w /app php:8.2-cli composer test
```

## CI/CD Testing

The package includes GitHub Actions workflows that automatically test on:
- PHP 8.0, 8.1, 8.2, 8.3, 8.4
- Laravel 8.*, 9.*, 10.*, 11.*, 12.*
- Ubuntu and Windows environments