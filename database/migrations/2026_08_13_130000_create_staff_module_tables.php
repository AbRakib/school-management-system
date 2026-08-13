<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_teaching_staff')->default(false);
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('staff_id')->unique();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('designation_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->unique();
            $table->date('dob');
            $table->string('gender', 20);
            $table->date('joining_date');
            $table->text('qualification')->nullable();
            $table->text('address');
            $table->string('photo')->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->string('status', 30)->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('class_teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['class_id', 'section_id', 'subject_id', 'academic_session_id'],
                'class_subject_session_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_teacher_subject');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};
