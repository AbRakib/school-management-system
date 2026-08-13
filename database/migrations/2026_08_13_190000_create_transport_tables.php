<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->string('type');
            $table->unsignedInteger('capacity');
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->decimal('fare_amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('transport_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->time('pickup_time')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
        });

        Schema::create('student_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_stop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'academic_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_transports');
        Schema::dropIfExists('transport_stops');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('vehicles');
    }
};
