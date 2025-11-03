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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->json('settings')->nullable(); // Form settings like theme, validation rules
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->string('qr_code')->nullable(); // QR code file path
            $table->string('qr_code_url')->nullable(); // QR code URL
            $table->integer('submissions_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
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