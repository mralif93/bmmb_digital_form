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
        Schema::create('form_submission_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('form_submissions')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('form_fields')->onDelete('cascade');
            
            // Field Value - can be text or JSON depending on field type
            $table->text('field_value')->nullable(); // For text, number, email, etc.
            $table->json('field_value_json')->nullable(); // For arrays, objects (multiselect, etc.)
            $table->string('file_path')->nullable(); // For file uploads
            
            $table->timestamps();
            
            $table->index('submission_id');
            $table->index('field_id');
            
            // Ensure one value per field per submission
            $table->unique(['submission_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submission_data');
    }
};
