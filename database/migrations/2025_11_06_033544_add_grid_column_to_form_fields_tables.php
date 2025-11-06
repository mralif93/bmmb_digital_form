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
        // Add grid_column to RAF form fields
        Schema::table('raf_form_fields', function (Blueprint $table) {
            $table->enum('grid_column', ['left', 'right', 'full'])->default('left')->after('sort_order');
        });

        // Add grid_column to DAR form fields
        Schema::table('dar_form_fields', function (Blueprint $table) {
            $table->enum('grid_column', ['left', 'right', 'full'])->default('left')->after('sort_order');
        });

        // Add grid_column to DCR form fields
        Schema::table('dcr_form_fields', function (Blueprint $table) {
            $table->enum('grid_column', ['left', 'right', 'full'])->default('left')->after('sort_order');
        });

        // Add grid_column to SRF form fields
        Schema::table('srf_form_fields', function (Blueprint $table) {
            $table->enum('grid_column', ['left', 'right', 'full'])->default('left')->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raf_form_fields', function (Blueprint $table) {
            $table->dropColumn('grid_column');
        });

        Schema::table('dar_form_fields', function (Blueprint $table) {
            $table->dropColumn('grid_column');
        });

        Schema::table('dcr_form_fields', function (Blueprint $table) {
            $table->dropColumn('grid_column');
        });

        Schema::table('srf_form_fields', function (Blueprint $table) {
            $table->dropColumn('grid_column');
        });
    }
};
