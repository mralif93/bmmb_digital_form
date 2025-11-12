<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use UsesSystemTimezone;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get branches for assigning to branch staff
        $branches = Branch::orderBy('branch_name')->get();
        $firstBranch = $branches->first();
        $secondBranch = $branches->skip(1)->first();
        $thirdBranch = $branches->skip(2)->first();
        
        // Create Admin User
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'role' => 'admin',
            'status' => 'active',
            'bio' => 'System administrator with full access to all features.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subHours(2),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => null, // Admin doesn't need a branch
        ]);

        // Create Branch Manager (BM) - Assign to first branch
        User::create([
            'first_name' => 'Ahmad',
            'last_name' => 'Hassan',
            'email' => 'bm@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'role' => 'branch_manager',
            'status' => 'active',
            'bio' => 'Branch Manager responsible for branch operations and management.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subDays(1),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => $firstBranch?->id, // Assign to first branch
        ]);

        // Create Assistant Branch Manager (ABM) - Assign to second branch
        User::create([
            'first_name' => 'Siti',
            'last_name' => 'Rahman',
            'email' => 'abm@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567892',
            'role' => 'assistant_branch_manager',
            'status' => 'active',
            'bio' => 'Assistant Branch Manager supporting branch operations.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subHours(5),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => $secondBranch?->id ?? $firstBranch?->id, // Assign to second branch, fallback to first
        ]);

        // Create Operations Officer (OO) - Assign to third branch
        User::create([
            'first_name' => 'Mohd',
            'last_name' => 'Ali',
            'email' => 'oo@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567893',
            'role' => 'operation_officer',
            'status' => 'active',
            'bio' => 'Operations Officer handling daily operational tasks.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subDays(2),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => $thirdBranch?->id ?? $firstBranch?->id, // Assign to third branch, fallback to first
        ]);

        // Create Headquarters (HQ) Users
        User::create([
            'first_name' => 'Fatimah',
            'last_name' => 'Ibrahim',
            'email' => 'hq@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567894',
            'role' => 'headquarters',
            'status' => 'active',
            'bio' => 'Headquarters staff member managing central operations.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subWeeks(2),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => null, // HQ users typically don't belong to a specific branch
        ]);

        User::create([
            'first_name' => 'Zainal',
            'last_name' => 'Ahmad',
            'email' => 'zainal.ahmad@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567895',
            'role' => 'headquarters',
            'status' => 'inactive',
            'bio' => 'Headquarters staff member.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subMonths(1),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => null, // HQ users typically don't belong to a specific branch
        ]);

        // Create Identity & Access Management (IAM) User
        User::create([
            'first_name' => 'Nur',
            'last_name' => 'Azmi',
            'email' => 'iam@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567896',
            'role' => 'iam',
            'status' => 'active',
            'bio' => 'Identity & Access Management officer responsible for user management and access control.',
            'email_verified_at' => $this->nowInSystemTimezone(),
            'last_login_at' => $this->nowInSystemTimezone()->subHours(1),
            'last_login_ip' => '127.0.0.1',
            'branch_id' => null, // IAM users typically don't belong to a specific branch
        ]);
    }
}
