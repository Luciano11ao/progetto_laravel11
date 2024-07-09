<?php

namespace Database\Seeders\Maia;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Commission;

class CommissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Commission::create([
            'id' => 1,
            'name' => 'Commessa maia <mionome>',
            'description' => 'Esercizio PHP base dati clack',
        ]);
    }
}
