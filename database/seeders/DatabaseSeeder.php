<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Maia\CommissionSeed;
use Database\Seeders\Maia\ServiceSeed;
use Database\Seeders\Maia\AssetClassSeed;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CommissionSeed::class,
            ServiceSeed::class,
            AssetClassSeed::class,
        ]);
    }
}
