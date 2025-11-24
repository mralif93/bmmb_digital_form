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
            $table->foreignId('section_id')->constrained('form_sections')->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->text('field_description')->nullable();
            $table->enum('field_type', [
                'text', 'email', 'phone', 'number', 'textarea', 'select', 
                'radio', 'checkbox', 'date', 'file', 'signature', 'currency',
                'multiselect', 'time', 'datetime', 'repeater', 'notes'
            ]);
            $table->string('field_placeholder')->nullable();
            $table->text('field_help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_conditional')->default(false);
            $table->json('conditional_logic')->nullable();
            $table->json('validation_rules')->nullable();
            $table->json('field_options')->nullable();
            $table->json('field_settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('grid_column', ['left', 'right', 'full'])->default('left');
            $table->boolean('is_active')->default(true);
            $table->string('css_class')->nullable();
            $table->json('custom_attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('form_id');
            $table->index('section_id');
            $table->index('field_type');
            $table->index('sort_order');
            $table->index('is_active');
            
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
