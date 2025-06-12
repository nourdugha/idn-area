<?php

declare(strict_types=1);

namespace zaidysf\IdnArea;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use zaidysf\IdnArea\Commands\IdnAreaCommand;

class IdnAreaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('idn-area')
            ->hasConfigFile()
            ->hasMigrations([
                'create_idn_provinces_table',
                'create_idn_regencies_table',
                'create_idn_districts_table',
                'create_idn_villages_table',
                'create_idn_islands_table',
            ])
            ->hasCommand(IdnAreaCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(IdnArea::class, function () {
            return new IdnArea;
        });
    }
}
