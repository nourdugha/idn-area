#!/bin/bash

# Indonesian Area Data Package Installation Script
# This script helps you set up the package in your Laravel application

echo "🇮🇩 Indonesian Area Data Package Setup"
echo "======================================="

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run from the root of a Laravel project"
    exit 1
fi

echo "✅ Laravel project detected"

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "📍 PHP Version: $PHP_VERSION"

# Install the package
echo "📦 Installing the package..."
composer require zaidysf/idn-area

if [ $? -ne 0 ]; then
    echo "❌ Failed to install the package"
    exit 1
fi

echo "✅ Package installed successfully"

# Publish migrations
echo "📄 Publishing migrations..."
php artisan vendor:publish --tag="idn-area-migrations"

if [ $? -ne 0 ]; then
    echo "❌ Failed to publish migrations"
    exit 1
fi

echo "✅ Migrations published"

# Ask if user wants to publish config
read -p "📋 Do you want to publish the config file? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan vendor:publish --tag="idn-area-config"
    echo "✅ Config file published"
fi

# Run migrations
read -p "🔧 Do you want to run migrations now? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate
    if [ $? -ne 0 ]; then
        echo "❌ Failed to run migrations"
        exit 1
    fi
    echo "✅ Migrations completed"
fi

# Seed data
read -p "🌱 Do you want to seed the Indonesian area data now? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "⏳ Seeding data... This may take a few minutes..."
    php artisan idn-area:seed
    if [ $? -ne 0 ]; then
        echo "❌ Failed to seed data"
        exit 1
    fi
    echo "✅ Data seeded successfully"
fi

echo ""
echo "🎉 Setup completed!"
echo ""
echo "📚 Quick Start:"
echo "==============="
echo ""
echo "Using Facade:"
echo "use zaidysf\\IdnArea\\Facades\\IdnArea;"
echo ""
echo "\$provinces = IdnArea::provinces();"
echo "\$regencies = IdnArea::regenciesByProvince('32');"
echo "\$results = IdnArea::search('Jakarta');"
echo ""
echo "Using Models:"
echo "use zaidysf\\IdnArea\\Models\\Province;"
echo ""
echo "\$province = Province::with('regencies')->find('32');"
echo ""
echo "📖 For more examples, check: examples/usage_examples.php"
echo "📋 Documentation: README.md"
echo ""
echo "🚀 Happy coding!"