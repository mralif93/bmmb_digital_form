<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_sections', function (Blueprint $table) {
            $table->string('grid_layout', 20)->default('2-column')
                ->after('is_active')
                ->comment('Grid layout: 2-column (standard) or 6-column (flexible)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_sections', function (Blueprint $table) {
            $table->dropColumn('grid_layout');
        });
    }
};
