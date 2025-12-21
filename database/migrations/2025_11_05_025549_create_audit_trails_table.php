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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User who performed the action
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // App\Models\Branch, App\Models\User, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->string('description')->nullable(); // Human-readable description
            $table->json('old_values')->nullable(); // Previous values (for updates)
            $table->json('new_values')->nullable(); // New values (for updates/creates)
            $table->string('ip_address', 45)->nullable(); // IP address of the user
            $table->text('user_agent')->nullable(); // User agent/browser info
            $table->string('url')->nullable(); // URL where action was performed
            $table->string('method')->nullable(); // HTTP method (GET, POST, PUT, DELETE)
            $table->json('request_data')->nullable(); // Request data (sanitized)
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
