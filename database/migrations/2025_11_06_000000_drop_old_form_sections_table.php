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
        // Drop the old form_sections table if it exists (with form_type column)
        Schema::dropIfExists('form_sections');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old table structure if needed for rollback
        // Note: This is the old structure with form_type, not form_id
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->enum('form_type', ['raf', 'dar', 'dcr', 'srf'])->index();
            $table->string('section_key')->index();
            $table->string('section_label');
            $table->text('section_description')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            
            $table->unique(['form_type', 'section_key']);
        });
    }
};
