<?php

declare(strict_types=1);

namespace zaidysf\IdnArea\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use zaidysf\IdnArea\Models\District;
use zaidysf\IdnArea\Models\Island;
use zaidysf\IdnArea\Models\Province;
use zaidysf\IdnArea\Models\Regency;
use zaidysf\IdnArea\Models\Village;

class IdnAreaSeeder
{
    protected string $dataPath;

    public function __construct()
    {
        $this->dataPath = __DIR__.'/../../database/data/';
    }

    public function seed(bool $force = false, ?Command $command = null): void
    {
        $this->checkTables();

        if (! $force && $this->hasData()) {
            if ($command) {
                $command->warn('Data already exists. Use --force to reseed.');
            }

            return;
        }

        DB::beginTransaction();

        try {
            if ($force) {
                $this->clearData($command);
            }

            $this->seedProvinces($command);
            $this->seedRegencies($command);
            $this->seedDistricts($command);
            $this->seedVillages($command);
            $this->seedIslands($command);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function checkTables(): void
    {
        $tables = ['idn_provinces', 'idn_regencies', 'idn_districts', 'idn_villages', 'idn_islands'];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                throw new \Exception("Table {$table} does not exist. Please run migrations first.");
            }
        }
    }

    protected function hasData(): bool
    {
        return Province::count() > 0 ||
               Regency::count() > 0 ||
               District::count() > 0 ||
               Village::count() > 0 ||
               Island::count() > 0;
    }

    protected function clearData(?Command $command = null): void
    {
        if ($command) {
            $command->info('Clearing existing data...');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Village::truncate();
        District::truncate();
        Regency::truncate();
        Province::truncate();
        Island::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function seedProvinces(?Command $command = null): void
    {
        if ($command) {
            $command->info('Seeding provinces...');
        }

        $csvFile = $this->dataPath.'provinces.csv';
        $data = $this->readCsv($csvFile);

        $provinces = [];
        foreach ($data as $row) {
            $provinces[] = [
                'code' => $row['code'],
                'name' => $row['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Province::insert($provinces);

        if ($command) {
            $command->info('Seeded '.count($provinces).' provinces');
        }
    }

    protected function seedRegencies(?Command $command = null): void
    {
        if ($command) {
            $command->info('Seeding regencies...');
        }

        $csvFile = $this->dataPath.'regencies.csv';
        $data = $this->readCsv($csvFile);

        $regencies = [];
        foreach ($data as $row) {
            $regencies[] = [
                'code' => $row['code'],
                'province_code' => $row['province_code'],
                'name' => $row['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($regencies, 100) as $chunk) {
            Regency::insert($chunk);
        }

        if ($command) {
            $command->info('Seeded '.count($regencies).' regencies');
        }
    }

    protected function seedDistricts(?Command $command = null): void
    {
        if ($command) {
            $command->info('Seeding districts...');
        }

        $csvFile = $this->dataPath.'districts.csv';
        $data = $this->readCsv($csvFile);

        $districts = [];
        foreach ($data as $row) {
            $districts[] = [
                'code' => $row['code'],
                'regency_code' => $row['regency_code'],
                'name' => $row['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($districts, 100) as $chunk) {
            District::insert($chunk);
        }

        if ($command) {
            $command->info('Seeded '.count($districts).' districts');
        }
    }

    protected function seedVillages(?Command $command = null): void
    {
        if ($command) {
            $command->info('Seeding villages...');
        }

        $csvFile = $this->dataPath.'villages.csv';
        $data = $this->readCsv($csvFile);

        $villages = [];
        $count = 0;

        foreach ($data as $row) {
            $villages[] = [
                'code' => $row['code'],
                'district_code' => $row['district_code'],
                'name' => $row['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert in smaller chunks for villages due to large dataset
            if (count($villages) >= 50) {
                Village::insert($villages);
                $villages = [];

                if ($command && $count % 1000 == 0) {
                    $command->info("Seeded {$count} villages...");
                }
            }
        }

        // Insert remaining villages
        if (! empty($villages)) {
            Village::insert($villages);
        }

        if ($command) {
            $command->info('Seeded '.$count.' villages');
        }
    }

    protected function seedIslands(?Command $command = null): void
    {
        if ($command) {
            $command->info('Seeding islands...');
        }

        $csvFile = $this->dataPath.'islands.csv';
        $data = $this->readCsv($csvFile);

        $islands = [];
        foreach ($data as $row) {
            $islands[] = [
                'code' => $row['code'] ?? null,
                'coordinate' => $row['coordinate'] ?? null,
                'name' => $row['name'],
                'is_outermost_small' => $this->parseBool($row['is_outermost_small'] ?? false),
                'is_populated' => $this->parseBool($row['is_populated'] ?? false),
                'regency_code' => $row['regency_code'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($islands, 100) as $chunk) {
            Island::insert($chunk);
        }

        if ($command) {
            $command->info('Seeded '.count($islands).' islands');
        }
    }

    protected function readCsv(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new \Exception("CSV file not found: {$filePath}");
        }

        $data = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \Exception("Could not open CSV file: {$filePath}");
        }

        // Read header row
        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);
            throw new \Exception("Could not read headers from CSV file: {$filePath}");
        }

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        return $data;
    }

    protected function parseBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }

        return (bool) $value;
    }
}
