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
        Schema::dropIfExists('form_sections'); // Drop old table with form_type if exists
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->string('section_key')->index(); // Unique identifier (e.g., 'applicant_info')
            $table->string('section_label'); // Display name (e.g., 'Applicant Information')
            $table->text('section_description')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint: section_key must be unique per form
            $table->unique(['form_id', 'section_key']);
            $table->index('form_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_sections');
    }
};
