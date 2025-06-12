<?php

// config for zaidysf/IdnArea
return [
    /*
    |--------------------------------------------------------------------------
    | Default Table Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the default table prefix used for Indonesian area tables.
    | You can change this if you want to use a different prefix.
    |
    */
    'table_prefix' => env('IDN_AREA_TABLE_PREFIX', 'idn_'),

    /*
    |--------------------------------------------------------------------------
    | Enable Foreign Key Constraints
    |--------------------------------------------------------------------------
    |
    | This option controls whether foreign key constraints should be enabled
    | for the Indonesian area tables. Disable this if you encounter issues
    | with your database engine.
    |
    */
    'enable_foreign_keys' => env('IDN_AREA_ENABLE_FOREIGN_KEYS', true),

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | These options control the search behavior of the package.
    |
    */
    'search' => [
        'village_limit' => env('IDN_AREA_VILLAGE_SEARCH_LIMIT', 100),
        'case_sensitive' => env('IDN_AREA_SEARCH_CASE_SENSITIVE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    |
    | You can override the default models used by the package by specifying
    | your own model classes here.
    |
    */
    'models' => [
        'province' => \zaidysf\IdnArea\Models\Province::class,
        'regency' => \zaidysf\IdnArea\Models\Regency::class,
        'district' => \zaidysf\IdnArea\Models\District::class,
        'village' => \zaidysf\IdnArea\Models\Village::class,
        'island' => \zaidysf\IdnArea\Models\Island::class,
    ],
];
