<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\SrfFormSubmission;
use App\Models\ServiceRequestForm;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicSrfSubmissionSeeder extends Seeder
{
    use UsesSystemTimezone;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first SRF form
        $srfForm = ServiceRequestForm::first();
        if (!$srfForm) {
            $this->command->warn('No SRF form found. Please create a form first before seeding submissions.');
            return;
        }

        // Get branches for branch linking
        $branches = Branch::all();

        // Sample data for 5 public submissions
        for ($i = 1; $i <= 5; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 30))->subHours(rand(1, 23));
            $submittedAt = $startedAt->copy()->addMinutes(rand(10, 60));
            
            // Randomly assign branch (some submissions may be from QR code)
            $branch = $branches->isNotEmpty() && rand(0, 1) ? $branches->random() : null;

            // Generate realistic form data
            $customerName = fake()->name();
            $customerEmail = fake()->email();
            $customerPhone = fake()->phoneNumber();
            
            $serviceTypes = ['deposit', 'withdrawal', 'transfer', 'account_opening', 'account_closing', 'statement_request', 'card_services', 'other'];
            $priorities = ['low', 'medium', 'high', 'urgent'];
            $accountTypes = ['savings', 'current', 'fixed_deposit', 'investment', 'other'];
            $idTypes = ['passport', 'national_id', 'drivers_license', 'other'];

            SrfFormSubmission::create([
                'srf_form_id' => $srfForm->id,
                'user_id' => null, // Public submission (no user login)
                'branch_id' => $branch?->id,
                'submission_token' => Str::random(32) . '-' . time() . '_' . $i,
                'status' => 'submitted',
                'submission_data' => [
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'customer_address' => fake()->address(),
                    'customer_id_type' => $idTypes[array_rand($idTypes)],
                    'customer_id_number' => fake()->numerify('##########'),
                    'customer_id_expiry_date' => fake()->dateTimeBetween('+1 month', '+5 years')->format('Y-m-d'),
                    'account_number' => fake()->numerify('#########'),
                    'account_type' => $accountTypes[array_rand($accountTypes)],
                    'service_type' => $serviceTypes[array_rand($serviceTypes)],
                    'service_description' => fake()->paragraph(),
                    'requested_date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
                    'preferred_time' => fake()->time('H:i'),
                    'contact_preference' => fake()->randomElement(['email', 'phone', 'sms']),
                    'priority' => $priorities[array_rand($priorities)],
                    'additional_requirements' => fake()->sentence(),
                    'terms_agreement' => '1',
                ],
                'field_responses' => [
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'customer_id_type' => $idTypes[array_rand($idTypes)],
                    'customer_id_number' => fake()->numerify('##########'),
                    'account_number' => fake()->numerify('#########'),
                    'account_type' => $accountTypes[array_rand($accountTypes)],
                    'service_type' => $serviceTypes[array_rand($serviceTypes)],
                    'priority' => $priorities[array_rand($priorities)],
                ],
                'file_uploads' => [],
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'session_id' => 'public_session_' . uniqid() . '_' . $i,
                'started_at' => $startedAt,
                'submitted_at' => $submittedAt,
                'last_modified_at' => $submittedAt,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'rejection_reason' => null,
                'audit_trail' => [
                    [
                        'action' => 'created',
                        'timestamp' => $startedAt->toISOString(),
                        'source' => 'public_form',
                    ],
                    [
                        'action' => 'submitted',
                        'timestamp' => $submittedAt->toISOString(),
                        'source' => 'public_form',
                    ],
                ],
                'compliance_checks' => null,
                'internal_notes' => null,
            ]);
        }

        $this->command->info('Created 5 public SRF form submissions.');
    }
}

