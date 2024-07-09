<?php

namespace Database\Seeders\Maia;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetClass;

class AssetClassSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assetClasses = [
            ['id' => 1, 'name' => 'Quadro generale biticino', 'commission_id' => 1, 'service_id' => 1],
            ['id' => 2, 'name' => 'Presa corrente siemens', 'commission_id' => 1, 'service_id' => 1],
            ['id' => 3, 'name' => 'Interruttore biticino', 'commission_id' => 1, 'service_id' => 1],
            ['id' => 4, 'name' => 'Condizionatore daikin 9000btu', 'commission_id' => 1, 'service_id' => 2],
            ['id' => 5, 'name' => 'Condizionatore samsumg 12000btu', 'commission_id' => 1, 'service_id' => 2],
            ['id' => 6, 'name' => 'pannello 400w', 'commission_id' => 1, 'service_id' => 3],
            ['id' => 7, 'name' => 'pannello 600w', 'commission_id' => 1, 'service_id' => 3],
            ['id' => 8, 'name' => 'pannello 800w', 'commission_id' => 1, 'service_id' => 3],
            ['id' => 9, 'name' => 'lampada ingresso', 'commission_id' => 1, 'service_id' => 4],
            ['id' => 10, 'name' => 'lampada alogena 50w', 'commission_id' => 1, 'service_id' => 4],
            ['id' => 11, 'name' => 'lampada alogena 150w', 'commission_id' => 1, 'service_id' => 4],
            ['id' => 12, 'name' => 'Caldaia riello 35kw', 'commission_id' => 1, 'service_id' => 5],
            ['id' => 13, 'name' => 'Caldaia riello 55kw', 'commission_id' => 1, 'service_id' => 5],
        ];

        foreach ($assetClasses as $assetClass) {
            AssetClass::create($assetClass);
        }
    }
}
