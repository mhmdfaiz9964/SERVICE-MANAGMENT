<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id(); // Primary Key

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('mobile_number')->unique();

            // Job Details
            $table->string('job_role'); // e.g., Cleaner, AC Technician
            $table->string('profile_photo')->nullable();

            // Work Status
            $table->enum('availability_status', ['available', 'busy', 'inactive'])
                  ->default('available');
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};
