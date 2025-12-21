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
            $table->string('reference_number')->nullable()->unique()->after('submission_token');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropIndex(['reference_number']);
            $table->dropColumn('reference_number');
        });
    }
};
