<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('questions', function (Blueprint $table) {
    $table->id();                           // Auto-incrementing primary key
    $table->string('question_id')->unique(); // Unique identifier from CSV (like "2ok")
    $table->text('question');               // The actual question text
    $table->string('answer_a');             // First answer option
    $table->string('answer_b');             // Second answer option  
    $table->string('answer_c');             // Third answer option
    $table->char('correct_answer', 1);      // Single character: 'a', 'b', or 'c'
    $table->timestamps();                   // created_at and updated_at
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
