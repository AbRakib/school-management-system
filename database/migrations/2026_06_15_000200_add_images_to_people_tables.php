<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
