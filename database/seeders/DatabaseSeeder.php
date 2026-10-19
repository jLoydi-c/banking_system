<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $user = User::factory()->create([
        'name'  => 'Test User',
        'email' => 'test@example.com',
    ]);

    Account::create([
        'user_id'        => $user->id,
        'account_number' => 'ACC-' . random_int(100000, 999999),
        'balance'        => 1000,
    ]);
}
}
