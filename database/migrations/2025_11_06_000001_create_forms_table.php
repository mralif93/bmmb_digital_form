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
        Schema::dropIfExists('forms'); // Drop if exists to recreate with new structure
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->integer('sort_order')->default(0)->index(); // Display order for public pages
            $table->string('name'); // Form name/title
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->text('description')->nullable(); // Form description
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->boolean('is_public')->default(true); // Public or private form
            $table->boolean('allow_multiple_submissions')->default(true); // Allow multiple submissions per user
            $table->integer('submission_limit')->nullable(); // Max submissions allowed (null = unlimited)
            $table->json('settings')->nullable(); // Additional form-level settings
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('slug');
            $table->index('status');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
