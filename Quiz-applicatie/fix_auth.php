<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

echo "Checking database connection...\n";

try {
    // Check if users table exists and has role column
    if (!Schema::hasTable('users')) {
        echo "ERROR: Users table does not exist!\n";
        exit(1);
    }
    
    if (!Schema::hasColumn('users', 'role')) {
        echo "Adding role column to users table...\n";
        Schema::table('users', function ($table) {
            $table->string('role')->default('student')->after('email');
        });
    }
    
    echo "Database structure OK\n";
    
    // Clear and create users
    echo "Creating users...\n";
    User::truncate();
    
    // Create Teachers
    $teacher1 = User::create([
        'name' => 'Docent Jan',
        'email' => 'docent@quiz.nl',
        'password' => Hash::make('docent123'),
        'role' => 'teacher',
        'email_verified_at' => now(),
    ]);
    echo "Created teacher: {$teacher1->email}\n";
    
    $teacher2 = User::create([
        'name' => 'Docent Maria',
        'email' => 'maria@quiz.nl',
        'password' => Hash::make('docent123'),
        'role' => 'teacher',
        'email_verified_at' => now(),
    ]);
    echo "Created teacher: {$teacher2->email}\n";
    
    // Create Students
    $student1 = User::create([
        'name' => 'Student Piet',
        'email' => 'student@quiz.nl',
        'password' => Hash::make('student123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
    echo "Created student: {$student1->email}\n";
    
    $student2 = User::create([
        'name' => 'Student Lisa',
        'email' => 'lisa@quiz.nl',
        'password' => Hash::make('student123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
    echo "Created student: {$student2->email}\n";
    
    $student3 = User::create([
        'name' => 'Student Tom',
        'email' => 'tom@quiz.nl',
        'password' => Hash::make('student123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
    echo "Created student: {$student3->email}\n";
    
    echo "\n=== LOGIN CREDENTIALS ===\n";
    echo "Teachers:\n";
    echo "- docent@quiz.nl / docent123\n";
    echo "- maria@quiz.nl / docent123\n";
    echo "\nStudents:\n";
    echo "- student@quiz.nl / student123\n";
    echo "- lisa@quiz.nl / student123\n";
    echo "- tom@quiz.nl / student123\n";
    echo "\nTotal users created: " . User::count() . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
