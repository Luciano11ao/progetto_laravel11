<?php

namespace Database\Seeders\Maia;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['id' => 1, 'name' => 'Elettrico', 'commission_id' => 1],
            ['id' => 2, 'name' => 'Climatizzazione', 'commission_id' => 1],
            ['id' => 3, 'name' => 'Fotovoltaico', 'commission_id' => 1],
            ['id' => 4, 'name' => 'Illuminazione', 'commission_id' => 1],
            ['id' => 5, 'name' => 'Riscaldamento', 'commission_id' => 1],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
