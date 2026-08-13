<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academic_sessions')) {
            Schema::create('academic_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status', 30)->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
                $table->string('name');
                $table->timestamps();
                $table->unique(['school_class_id', 'name']);
            });
        }

        if (! Schema::hasTable('student_categories')) {
            Schema::create('student_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('guardians')) {
            Schema::table('guardians', function (Blueprint $table) {
                if (! Schema::hasColumn('guardians', 'relation')) {
                    $table->string('relation', 30)->default('guardian')->after('email');
                }
            });

            if (Schema::hasColumn('guardians', 'relationship')) {
                DB::table('guardians')
                    ->whereNull('relation')
                    ->orWhere('relation', '')
                    ->update(['relation' => DB::raw('LOWER(relationship)')]);
            }
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                }
                if (! Schema::hasColumn('students', 'admission_no')) {
                    $table->string('admission_no')->nullable()->unique()->after('user_id');
                }
                if (! Schema::hasColumn('students', 'class_id')) {
                    $table->foreignId('class_id')->nullable()->after('admission_no')->constrained('school_classes')->restrictOnDelete();
                }
                if (! Schema::hasColumn('students', 'section_id')) {
                    $table->foreignId('section_id')->nullable()->after('class_id')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('students', 'academic_session_id')) {
                    $table->foreignId('academic_session_id')->nullable()->after('section_id')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('students', 'roll_no')) {
                    $table->string('roll_no', 50)->nullable()->after('academic_session_id');
                }
                if (! Schema::hasColumn('students', 'student_category_id')) {
                    $table->foreignId('student_category_id')->nullable()->after('guardian_id')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('students', 'dob')) {
                    $table->date('dob')->nullable()->after('name');
                }
                if (! Schema::hasColumn('students', 'blood_group')) {
                    $table->string('blood_group', 10)->nullable()->after('gender');
                }
                if (! Schema::hasColumn('students', 'photo')) {
                    $table->string('photo')->nullable()->after('blood_group');
                }
                if (! Schema::hasColumn('students', 'religion')) {
                    $table->string('religion')->nullable()->after('photo');
                }
                if (! Schema::hasColumn('students', 'present_address')) {
                    $table->text('present_address')->nullable()->after('religion');
                }
                if (! Schema::hasColumn('students', 'permanent_address')) {
                    $table->text('permanent_address')->nullable()->after('present_address');
                }
                if (! Schema::hasColumn('students', 'status')) {
                    $table->string('status', 30)->default('active')->after('admission_date');
                }
                if (! Schema::hasColumn('students', 'deleted_at')) {
                    $table->softDeletes();
                }
            });

            DB::table('students')
                ->whereNull('admission_no')
                ->update(['admission_no' => DB::raw('admission_number')]);

            DB::table('students')
                ->whereNull('class_id')
                ->update(['class_id' => DB::raw('school_class_id')]);

            DB::table('students')
                ->whereNull('dob')
                ->update(['dob' => DB::raw('date_of_birth')]);

            DB::table('students')
                ->whereNull('present_address')
                ->update(['present_address' => DB::raw('address')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                foreach ([
                    'deleted_at',
                    'status',
                    'permanent_address',
                    'present_address',
                    'religion',
                    'photo',
                    'blood_group',
                    'dob',
                    'student_category_id',
                    'roll_no',
                    'academic_session_id',
                    'section_id',
                    'class_id',
                    'admission_no',
                    'user_id',
                ] as $column) {
                    if (Schema::hasColumn('students', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('guardians') && Schema::hasColumn('guardians', 'relation')) {
            Schema::table('guardians', fn (Blueprint $table) => $table->dropColumn('relation'));
        }

        Schema::dropIfExists('student_categories');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('academic_sessions');
    }
};
