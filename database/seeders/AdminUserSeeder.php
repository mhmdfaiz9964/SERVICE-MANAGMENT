<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Mhmd',
                'last_name'  => 'Faiz',
                'mobile'     => '0770597445', // optional
                'password'   => Hash::make('123456'),
            ]
        );
    }
}
