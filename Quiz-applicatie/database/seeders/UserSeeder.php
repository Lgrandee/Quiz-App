<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Teachers
        User::create([
            'name' => 'Docent Jan',
            'email' => 'docent@quiz.nl',
            'password' => Hash::make('docent123'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Docent Maria',
            'email' => 'maria@quiz.nl',
            'password' => Hash::make('docent123'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        // Create Students
        User::create([
            'name' => 'Student Piet',
            'email' => 'student@quiz.nl',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student Lisa',
            'email' => 'lisa@quiz.nl',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student Tom',
            'email' => 'tom@quiz.nl',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student Emma',
            'email' => 'emma@quiz.nl',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student Max',
            'email' => 'max@quiz.nl',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Student Sophie',
            'email' => 'sophie@quiz.nl',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }
}
