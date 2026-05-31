<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OfficerSeeder extends Seeder
{
    public function run(): void
    {
        $officers = [
            ['name' => 'Demo Officer',     'email' => 'demo@jpn.gov.my',     'role' => 'officer'],
            ['name' => 'Aisyah binti Yusof', 'email' => 'aisyah@jpn.gov.my',   'role' => 'officer'],
            ['name' => 'Hafiz bin Razak',    'email' => 'hafiz@jpn.gov.my',    'role' => 'officer'],
            ['name' => 'Nurul Iman',         'email' => 'nurul@jpn.gov.my',    'role' => 'supervisor'],
            ['name' => 'Encik Ibrahim',      'email' => 'ibrahim@jpn.gov.my',  'role' => 'admin'],
        ];

        foreach ($officers as $officer) {
            User::updateOrCreate(
                ['email' => $officer['email']],
                [
                    'name' => $officer['name'],
                    'password' => Hash::make('password'),
                    'role' => $officer['role'],
                    'email_verified_at' => now(),
                ],
            );
        }

        $this->command?->info('Seeded ' . count($officers) . ' officers. Demo login: demo@jpn.gov.my / password');
    }
}
