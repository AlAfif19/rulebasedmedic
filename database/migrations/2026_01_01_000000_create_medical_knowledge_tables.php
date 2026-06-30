<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->string('category', 60)->nullable();
            $table->string('duration', 60)->nullable();
            $table->string('body_location', 60)->nullable();
            $table->string('frequency', 60)->nullable();
            $table->decimal('weight', 4, 2)->default(0.80);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->text('solution')->nullable();
            $table->string('severity', 40)->default('Ringan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->foreignId('disease_id')->nullable()->constrained('diseases')->nullOnDelete();
            $table->string('name', 160);
            $table->string('category', 80)->nullable();
            $table->string('dosage', 120)->nullable();
            $table->text('usage_rule')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('contraindication')->nullable();
            $table->text('warning')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->foreignId('disease_id')->constrained('diseases')->cascadeOnDelete();
            $table->json('symptom_codes');
            $table->json('medicine_codes');
            $table->decimal('cf_value', 4, 2)->default(0.85);
            $table->enum('method', ['forward', 'backward', 'certainty', 'parallel'])->default('parallel');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('disease_id')->nullable()->constrained('diseases')->nullOnDelete();
            $table->enum('method', ['forward', 'backward', 'certainty', 'parallel'])->default('parallel');
            $table->json('selected_symptom_codes');
            $table->json('result_payload')->nullable();
            $table->decimal('confidence_score', 5, 2)->default(0);
            $table->string('status', 40)->default('matched');
            $table->text('notes')->nullable();
            $table->text('recommendation_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('diseases');
        Schema::dropIfExists('symptoms');
    }
};
