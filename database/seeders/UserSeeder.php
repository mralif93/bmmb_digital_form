<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            'email_verified_at' => now(),
            'last_login_at' => now()->subHours(2),
            'last_login_ip' => '127.0.0.1',
        ]);

        // Create Moderator User
        User::create([
            'first_name' => 'John',
            'last_name' => 'Moderator',
            'email' => 'moderator@bmmb.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'role' => 'moderator',
            'status' => 'active',
            'bio' => 'Content moderator responsible for reviewing user submissions.',
            'email_verified_at' => now(),
            'last_login_at' => now()->subDays(1),
            'last_login_ip' => '127.0.0.1',
        ]);

        // Create Regular Users
        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567892',
            'role' => 'user',
            'status' => 'active',
            'bio' => 'Regular user who creates and manages forms.',
            'email_verified_at' => now(),
            'last_login_at' => now()->subHours(5),
            'last_login_ip' => '127.0.0.1',
        ]);

        User::create([
            'first_name' => 'Mike',
            'last_name' => 'Johnson',
            'email' => 'mike.johnson@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567893',
            'role' => 'user',
            'status' => 'active',
            'bio' => 'Marketing professional using forms for lead generation.',
            'email_verified_at' => now(),
            'last_login_at' => now()->subDays(2),
            'last_login_ip' => '127.0.0.1',
        ]);

        User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Wilson',
            'email' => 'sarah.wilson@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567894',
            'role' => 'user',
            'status' => 'inactive',
            'bio' => 'Event coordinator who uses forms for event registrations.',
            'email_verified_at' => now(),
            'last_login_at' => now()->subWeeks(2),
            'last_login_ip' => '127.0.0.1',
        ]);

        User::create([
            'first_name' => 'David',
            'last_name' => 'Brown',
            'email' => 'david.brown@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567895',
            'role' => 'user',
            'status' => 'suspended',
            'bio' => 'Account suspended due to policy violations.',
            'email_verified_at' => now(),
            'last_login_at' => now()->subMonths(1),
            'last_login_ip' => '127.0.0.1',
        ]);
    }
}
