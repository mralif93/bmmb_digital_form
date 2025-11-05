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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name'); // "ALAM DAMAI"
            $table->enum('weekend_start_day', ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'])->default('SATURDAY');
            $table->string('ti_agent_code')->unique(); // "FN12984"
            $table->text('address'); // Full address
            $table->string('email')->unique(); // "sgar@muamalat.com.my"
            $table->string('state'); // "Wilayah Persekutuan Kuala Lumpur"
            $table->string('region'); // "Central 1"
            $table->timestamps();
            
            $table->index('branch_name');
            $table->index('state');
            $table->index('region');
        });

        // Note: Foreign key constraints for branch_id in submission tables
        // are added after the branches table is created to avoid SQLite issues.
        // They will be added in a separate migration if needed, but SQLite doesn't
        // handle foreign keys on existing tables well, so we rely on application-level integrity.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
