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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->string('field_section'); // Section name for grouping fields
            $table->string('field_name'); // Unique identifier within the form
            $table->string('field_label');
            $table->text('field_description')->nullable();
            $table->enum('field_type', [
                'text', 'email', 'phone', 'number', 'textarea', 'select', 
                'radio', 'checkbox', 'date', 'file', 'signature', 'currency',
                'multiselect', 'time', 'datetime'
            ]);
            $table->string('field_placeholder')->nullable();
            $table->text('field_help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_conditional')->default(false);
            $table->json('conditional_logic')->nullable(); // Show/hide based on other fields
            $table->json('validation_rules')->nullable(); // Custom validation rules
            $table->json('field_options')->nullable(); // For select, radio, checkbox options
            $table->json('field_settings')->nullable(); // Additional field-specific settings
            $table->integer('sort_order')->default(0);
            $table->enum('grid_column', ['left', 'right', 'full'])->default('left');
            $table->boolean('is_active')->default(true);
            $table->string('css_class')->nullable();
            $table->json('custom_attributes')->nullable();
            $table->timestamps();
            
            $table->index('form_id');
            $table->index('field_section');
            $table->index('field_type');
            $table->index('sort_order');
            $table->index('is_active');
            
            // Ensure field_name is unique within a form
            $table->unique(['form_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
