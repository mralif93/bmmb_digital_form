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
        // Add form_id to remittance_application_forms (RAF)
        Schema::table('remittance_application_forms', function (Blueprint $table) {
            $table->foreignId('form_id')->nullable()->after('id')->constrained('forms')->onDelete('cascade');
            $table->index('form_id');
        });

        // Add form_id to data_access_request_forms (DAR)
        Schema::table('data_access_request_forms', function (Blueprint $table) {
            $table->foreignId('form_id')->nullable()->after('id')->constrained('forms')->onDelete('cascade');
            $table->index('form_id');
        });

        // Add form_id to data_correction_request_forms (DCR)
        Schema::table('data_correction_request_forms', function (Blueprint $table) {
            $table->foreignId('form_id')->nullable()->after('id')->constrained('forms')->onDelete('cascade');
            $table->index('form_id');
        });

        // Add form_id to service_request_forms (SRF)
        Schema::table('service_request_forms', function (Blueprint $table) {
            $table->foreignId('form_id')->nullable()->after('id')->constrained('forms')->onDelete('cascade');
            $table->index('form_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remittance_application_forms', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropColumn('form_id');
        });

        Schema::table('data_access_request_forms', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropColumn('form_id');
        });

        Schema::table('data_correction_request_forms', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropColumn('form_id');
        });

        Schema::table('service_request_forms', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropColumn('form_id');
        });
    }
};
