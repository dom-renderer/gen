<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Liklihood;

class LiklihoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Low', 'Moderate', 'High'] as $row) {
            Liklihood::updateOrCreate(['name' => $row], ['name' => $row]);
        }
    }
}
