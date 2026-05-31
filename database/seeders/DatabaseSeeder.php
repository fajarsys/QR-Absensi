<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@utama.com')],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'rahasia123')),
                'role' => 'admin',
                'jabatan' => 'Head of IT',
            ]
        );

        // 2. Akun KARYAWAN (Data Dummy untuk Testing Lokal)
        // Pastikan TIDAK menggunakan email atau nama asli orang di sini.
        User::updateOrCreate(
            ['email' => 'karyawan_dummy@example.com'],
            [
                'name' => 'Karyawan Tester',
                'password' => Hash::make('password'), // Aman untuk dipublikasi karena ini hanya dummy
                'role' => 'karyawan',
                'jabatan' => 'Staff Teknisi',
            ]
        );

        // 3. Buat Data KANTOR (Dummy Location)
        Office::create([
            'name' => 'Stmik Mercusuar',
            'latitude' => -6.281621859735902,
            'longitude' => 106.91391973919393,
            'radius' => 50
        ]);

        $this->call([
            ScheduleSeeder::class,
        ]);

        \App\Models\User::factory(10)->create();
    }
}