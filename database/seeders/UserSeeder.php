<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDO;

class UserSeeder extends Seeder
{
    /**
     * Seed users from MAP database.
     */
    public function run(): void
    {
        $this->command->info('Seeding users from MAP...');

        // First create admin user
        $this->createAdminUser();

        $mapDbPath = base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3');

        if (!file_exists($mapDbPath)) {
            $this->command->error("MAP database not found at: {$mapDbPath}");
            return;
        }

        try {
            $mapDb = new PDO("sqlite:{$mapDbPath}");
            $mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Only get active users
            $stmt = $mapDb->query("
                SELECT 
                    u.id as map_user_id,
                    u.username,
                    u.email,
                    u.first_name,
                    u.last_name,
                    u.is_active,
                    s.position,
                    s.branch_id,
                    b.ti_agent_code as branch_code
                FROM user_user u
                LEFT JOIN user_staffprofile s ON u.id = s.user_id
                LEFT JOIN Application_branch b ON s.branch_id = b.id
                WHERE u.is_active = 1
                ORDER BY u.id
            ");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $created = 0;
            $updated = 0;
            $errors = 0;

            foreach ($users as $mapUser) {
                try {
                    // Find branch by ti_agent_code
                    $branchId = null;
                    if ($mapUser['branch_code']) {
                        $branch = Branch::where('ti_agent_code', $mapUser['branch_code'])->first();
                        $branchId = $branch?->id;
                    }

                    // Map position to role
                    $role = $this->mapPositionToRole($mapUser['position'] ?? '1');

                    // Check if user exists
                    $existingUser = User::withTrashed()
                        ->where('map_user_id', $mapUser['map_user_id'])
                        ->orWhere('email', $mapUser['email'])
                        ->first();

                    if ($existingUser) {
                        $existingUser->update([
                            'map_user_id' => $mapUser['map_user_id'],
                            'username' => $mapUser['username'],
                            'email' => $mapUser['email'],
                            'first_name' => $mapUser['first_name'],
                            'last_name' => $mapUser['last_name'],
                            'map_position' => $mapUser['position'] ?? '1',
                            'role' => $role,
                            'branch_id' => $branchId,
                            'status' => 'active',
                            'is_map_synced' => true,
                            'map_last_sync' => now(),
                            'deleted_at' => null,
                        ]);
                        $updated++;
                    } else {
                        User::create([
                            'map_user_id' => $mapUser['map_user_id'],
                            'username' => $mapUser['username'],
                            'email' => $mapUser['email'],
                            'first_name' => $mapUser['first_name'],
                            'last_name' => $mapUser['last_name'],
                            'password' => Hash::make(Str::random(32)),
                            'map_position' => $mapUser['position'] ?? '1',
                            'role' => $role,
                            'branch_id' => $branchId,
                            'status' => 'active',
                            'is_map_synced' => true,
                            'map_last_sync' => now(),
                        ]);
                        $created++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('User seeding error', [
                        'user' => $mapUser['email'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->command->info("✓ Created {$created} users, Updated {$updated} users" . ($errors > 0 ? ", {$errors} errors" : ""));

        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
            Log::error('User seeding error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create default admin user.
     */
    private function createAdminUser(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bmmb.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
        $this->command->info('✓ Admin user created/updated');
    }

    /**
     * Map MAP position to eForm role.
     */
    private function mapPositionToRole(?string $position): string
    {
        return match ($position) {
            '1' => 'headquarters',
            '2' => 'branch_manager',
            '3' => 'cfe',
            '4' => 'headquarters',
            '9', '10' => 'operation_officer',
            default => 'headquarters',
        };
    }
}
