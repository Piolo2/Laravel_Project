<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('contact_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('civil_status')->nullable();
            $table->text('address')->nullable(); // Full address

            // Professional Information
            $table->integer('years_experience')->nullable();
            $table->json('skill_types')->nullable(); // Store selected skills as JSON
            $table->string('service_type')->nullable();
            $table->string('education_attainment')->nullable();
            $table->string('work_status')->nullable();

            // Compliance & Documents
            $table->boolean('has_compliance_certificates')->default(false);
            $table->string('compliance_certificate_file')->nullable();

            // ID Information
            $table->string('id_type')->nullable();
            $table->string('id_front_file')->nullable();
            $table->string('id_back_file')->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_verifications');
    }
};
