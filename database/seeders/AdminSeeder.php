<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat akun admin default.
     */
    public function run(): void
    {
        // Cek apakah admin sudah ada
        $existing = DB::table('users')->where('email', 'admin@example.com')->first();
        if ($existing) {
            $this->command->info('Admin user sudah ada, skip seeding.');
            return;
        }

        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'is_admin' => 1,
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info('Admin user berhasil dibuat: admin@example.com / Admin123!');
    }
}
