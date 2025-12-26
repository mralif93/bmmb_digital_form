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
        Schema::table('form_submissions', function (Blueprint $table) {
            // Part F: Acknowledgment Receipt (FOR BMMB OFFICE USE ONLY)
            $table->string('acknowledgment_received_by')->nullable()->after('completion_notes');
            $table->date('acknowledgment_date_received')->nullable()->after('acknowledgment_received_by');
            $table->string('acknowledgment_staff_name')->nullable()->after('acknowledgment_date_received');
            $table->string('acknowledgment_designation')->nullable()->after('acknowledgment_staff_name');
            $table->string('acknowledgment_stamp')->nullable()->after('acknowledgment_designation');

            // Part G: Verification
            $table->string('verification_verified_by')->nullable()->after('acknowledgment_stamp');
            $table->date('verification_date')->nullable()->after('verification_verified_by');
            $table->string('verification_staff_name')->nullable()->after('verification_date');
            $table->string('verification_designation')->nullable()->after('verification_staff_name');
            $table->string('verification_stamp')->nullable()->after('verification_designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'acknowledgment_received_by',
                'acknowledgment_date_received',
                'acknowledgment_staff_name',
                'acknowledgment_designation',
                'acknowledgment_stamp',
                'verification_verified_by',
                'verification_date',
                'verification_staff_name',
                'verification_designation',
                'verification_stamp',
            ]);
        });
    }
};
