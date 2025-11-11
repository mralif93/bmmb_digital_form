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
            $table->softDeletes();
            
            $table->index('branch_name');
            $table->index('state');
            $table->index('region');
        });

        // Add foreign key constraint for branch_id in users table
        // This is safe because branches table is created before this constraint is added
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint before dropping branches table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
        
        Schema::dropIfExists('branches');
    }
};
