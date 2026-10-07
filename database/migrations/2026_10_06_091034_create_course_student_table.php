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
        Schema::create('course_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // FK defined in Student Model
            $table->unsignedBigInteger('course_id');  // FK defined in Course Model
            $table->timestamps();

            // Composite index for query performance
            $table->index(['student_id', 'course_id']);
            // unique(['student_id','course_id']) enforced in Model via $rules
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_student');
    }
};
