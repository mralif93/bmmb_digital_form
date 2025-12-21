<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Adds MAP (Muamalat Authentication Platform) integration fields to users table.
     * This allows eform to sync user data from the MAP system.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // MAP system reference
            $table->unsignedInteger('map_user_id')->nullable()->unique()->after('id');
            $table->unsignedInteger('map_staff_id')->nullable()->after('map_user_id');

            // Add username field (MAP uses username for login)
            $table->string('username')->nullable()->unique()->after('email');

            // MAP position mapping (from StaffProfile)
            $table->string('map_position', 2)->nullable()->after('role')
                ->comment('MAP position code: 1=HQ, 2=BM, 3=CFE, etc.');

            // Sync metadata
            $table->timestamp('map_last_sync')->nullable()->after('last_login_ip')
                ->comment('Last time user data was synced from MAP');
            $table->boolean('is_map_synced')->default(false)->after('map_last_sync')
                ->comment('Whether this user is synced from MAP');

            // Indexes for performance
            $table->index('map_user_id');
            $table->index('map_staff_id');
            $table->index(['is_map_synced', 'map_last_sync']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_map_user_id_unique']);
            $table->dropIndex(['users_map_user_id_index']);
            $table->dropIndex(['users_map_staff_id_index']);
            $table->dropIndex(['users_is_map_synced_map_last_sync_index']);

            $table->dropColumn([
                'map_user_id',
                'map_staff_id',
                'username',
                'map_position',
                'map_last_sync',
                'is_map_synced',
            ]);
        });
    }
};