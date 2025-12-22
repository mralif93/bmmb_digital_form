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
                    u.is_superuser,
                    s.position,
                    s.branch_id,
                    b.ti_agent_code as branch_code,
                    b.title as branch_name
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
                    // Trim data
                    $username = trim($mapUser['username']);
                    $email = trim($mapUser['email']);
                    $firstName = trim($mapUser['first_name']);
                    $lastName = trim($mapUser['last_name']);

                    // Find user by map_user_id (Primary Match)
                    $existingUser = User::withTrashed()->where('map_user_id', $mapUser['map_user_id'])->first();

                    // If not found, find by username (Secondary Match)
                    if (!$existingUser) {
                        $existingUser = User::withTrashed()->where('username', $username)->first();
                    }

                    // Prepare email - Handle collisions
                    // If email is empty string or null, set to null
                    $emailToUse = !empty($email) ? $email : null;

                    // Only check collision if we actually have an email
                    if ($emailToUse) {
                        // Check if email is taken by a DIFFERENT user
                        $emailTaken = User::withTrashed()
                            ->where('email', $emailToUse)
                            ->when($existingUser, function ($q) use ($existingUser) {
                                $q->where('id', '!=', $existingUser->id);
                            })
                            ->exists();

                        if ($emailTaken) {
                            // If collision happens, we set email to null since it's not unique
                            $emailToUse = null;
                        }
                    }

                    // Robust Branch Resolution
                    $branchId = null;
                    // 1. Code
                    if (!empty($mapUser['branch_code'])) {
                        $branch = Branch::where('ti_agent_code', $mapUser['branch_code'])->first();
                        $branchId = $branch?->id;
                    }
                    // 2. Name
                    if (!$branchId && !empty($mapUser['branch_name'])) {
                        $branch = Branch::where('branch_name', $mapUser['branch_name'])->first();
                        $branchId = $branch?->id;
                    }
                    // 3. ID
                    if (!$branchId && !empty($mapUser['branch_id'])) {
                        if (Branch::where('id', $mapUser['branch_id'])->exists()) {
                            $branchId = $mapUser['branch_id'];
                        }
                    }

                    // Map position to role
                    $role = $this->mapPositionToRole($mapUser['position'] ?? '1');

                    // OVERRIDE: If is_superuser in MAP, force admin role
                    if (!empty($mapUser['is_superuser']) && $mapUser['is_superuser'] == 1) {
                        $role = 'admin';
                    }

                    // Determine is_access_eform
                    $isAccessEform = true;
                    // Restriction: Superusers default to NO access, unless whitelisted
                    if (!empty($mapUser['is_superuser']) && $mapUser['is_superuser'] == 1) {
                        $whitelist = ['mralif93', 'naziha', 'zaki', 'zaid', 'digital'];
                        if (!in_array($username, $whitelist)) {
                            $isAccessEform = false;
                        }
                    }

                    $userData = [
                        'map_user_id' => $mapUser['map_user_id'],
                        'username' => $username,
                        'email' => $emailToUse,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'map_position' => $mapUser['position'] ?? '1',
                        'role' => $role,
                        'branch_id' => $branchId,
                        'status' => 'active',
                        'is_map_synced' => true,
                        'is_access_eform' => $isAccessEform,
                        'map_last_sync' => now(),
                        'deleted_at' => null,
                    ];

                    if ($existingUser) {
                        // Preserve existing admin role if this update wouldn't otherwise make them admin
                        if ($existingUser->role === 'admin' && $role !== 'admin') {
                            $userData['role'] = 'admin';
                        }

                        $existingUser->update($userData);
                        $updated++;
                    } else {
                        $userData['password'] = Hash::make(Str::random(32));
                        User::create($userData);
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
