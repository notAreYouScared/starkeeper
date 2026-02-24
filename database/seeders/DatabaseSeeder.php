<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'name'        => 'Security',
                'description' => 'Handles fleet protection, combat operations, and base defence across all theatres.',
            ],
            [
                'name'        => 'Industry',
                'description' => 'Manages mining, salvage, cargo hauling, and resource logistics for the organisation.',
            ],
            [
                'name'        => 'Racing',
                'description' => 'Competes in Murray Cup and other racing circuits, driving development of high-speed craft.',
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
