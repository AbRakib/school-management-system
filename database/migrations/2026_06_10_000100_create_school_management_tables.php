<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->string('designation')->default('teacher');
            $table->string('subject')->nullable();
            $table->date('joining_date')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('grade_level')->unique();
            $table->string('name');
            $table->string('section', 20)->nullable();
            $table->string('room', 50)->nullable();
            $table->foreignId('class_teacher_id')
                ->nullable()
                ->constrained('teachers')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('relationship', 50)->default('Parent');
            $table->string('phone', 30);
            $table->string('email')->nullable()->unique();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number')->unique();
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->foreignId('school_class_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('guardian_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('phone', 30)->nullable();
            $table->date('admission_date');
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('school_classes');
        Schema::dropIfExists('teachers');
    }
};
