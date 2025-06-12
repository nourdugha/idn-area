<?php

declare(strict_types=1);

namespace zaidysf\IdnArea\Commands;

use Illuminate\Console\Command;
use zaidysf\IdnArea\Services\IdnAreaSeeder;

class IdnAreaCommand extends Command
{
    public $signature = 'idn-area:seed {--force : Force seeding even if data exists}';

    public $description = 'Seed Indonesian area data (provinces, regencies, districts, villages, and islands)';

    public function handle(): int
    {
        $force = $this->option('force');

        $this->info('Starting Indonesian area data seeding...');

        $seeder = new IdnAreaSeeder;

        try {
            $seeder->seed($force, $this);
            $this->info('\nAll Indonesian area data has been seeded successfully!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error seeding data: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
