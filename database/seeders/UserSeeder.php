<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::whereEmail('employee@employee.com')->doesntExist()) {
            $userData = [
                'email' => 'employee@employee.com',
                'password' => Hash::make('employee@employee.com'),
                'role' => 'employee',
            ];

            User::create($userData)->userDetails()->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);
        }
    }
}