<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo / admin user (password: password) ──
        User::firstOrCreate(
            ['email' => 'demo@gdgoc.dev'],
            [
                'name'              => 'Demo GDGOC',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'arvin@gdgoc.dev'],
            [
                'name'              => 'Arvin (Trainer)',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // ── 8 random users for demo stats ──
        User::factory(8)->create();

        $this->command->info('✅ Users seeded: 10 total (2 fixed + 8 random)');
    }
}
