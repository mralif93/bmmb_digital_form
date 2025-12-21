<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQLite doesn't support modifying columns directly, so we need to:
        // 1. Create a new table with correct schema
        // 2. Copy data
        // 3. Drop old table
        // 4. Rename new table

        Schema::create('branches_new', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->enum('weekend_start_day', ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'])->default('SATURDAY');
            $table->string('ti_agent_code')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_name');
        });

        // Copy existing data if any
        DB::statement('INSERT INTO branches_new (id, branch_name, weekend_start_day, ti_agent_code, address, email, created_at, updated_at, deleted_at) 
                      SELECT id, branch_name, weekend_start_day, ti_agent_code, address, email, created_at, updated_at, deleted_at FROM branches');

        // Drop old table
        Schema::dropIfExists('branches');

        // Rename new table
        Schema::rename('branches_new', 'branches');
    }

    public function down(): void
    {
        // Reverse migration - recreate old structure
        Schema::create('branches_old', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->enum('weekend_start_day', ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'])->default('SATURDAY');
            $table->string('ti_agent_code');
            $table->text('address');
            $table->string('email');
            $table->string('state');
            $table->string('region');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('INSERT INTO branches_old SELECT * FROM branches');
        Schema::dropIfExists('branches');
        Schema::rename('branches_old', 'branches');
    }
};
