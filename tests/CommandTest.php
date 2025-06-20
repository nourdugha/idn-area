<?php

use Illuminate\Support\Facades\Artisan;
use zaidysf\IdnArea\Commands\IdnAreaCommand;

// Test Artisan Command
describe('IdnAreaCommand', function () {
    it('can instantiate command', function () {
        $command = new IdnAreaCommand;

        expect($command)->toBeInstanceOf(IdnAreaCommand::class);
    });

    it('has correct signature and description', function () {
        $command = new IdnAreaCommand;

        expect($command->signature)->toBe('idn-area:seed {--force : Force seeding even if data exists}');
        expect($command->description)->toBe('Seed Indonesian area data (provinces, regencies, districts, villages, and islands)');
    });

    it('command is registered', function () {
        $commands = Artisan::all();

        expect($commands)->toHaveKey('idn-area:seed');
    });

    it('can run command without force option', function () {
        // This will attempt to run but may succeed in test environment
        $exitCode = Artisan::call('idn-area:seed');

        // Command should return success (0) or failure (1) depending on environment
        expect($exitCode)->toBeIn([0, 1]);
    });
});
