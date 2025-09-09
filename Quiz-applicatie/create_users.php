<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Clear existing users
User::truncate();

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

echo "Users created successfully!\n";
echo "Total users: " . User::count() . "\n";

foreach (User::all() as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
}
