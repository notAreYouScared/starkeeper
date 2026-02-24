<?php

namespace Database\Seeders;

use App\Models\OrgRole;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $orgRoles = [
            ['name' => 'leadership', 'label' => 'Leadership', 'sort_order' => 1],
            ['name' => 'director',   'label' => 'Director',   'sort_order' => 2],
            ['name' => 'mod',        'label' => 'Mod',        'sort_order' => 3],
            ['name' => 'member',     'label' => 'Member',     'sort_order' => 4],
        ];

        foreach ($orgRoles as $role) {
            OrgRole::firstOrCreate(['name' => $role['name']], $role);
        }

        $units = [
            [
                'name' => 'Security',
                'description' => 'Handles fleet protection, combat operations, and base defence across all theatres.',
            ],
            [
                'name' => 'Industry',
                'description' => 'Manages mining, salvage, cargo hauling, and resource logistics for the organisation.',
            ],
            [
                'name' => 'Racing',
                'description' => 'Competes in Murray Cup and other racing circuits, driving development of high-speed craft.',
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
