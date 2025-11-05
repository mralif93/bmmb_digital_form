<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ServiceRequestForm;
use App\Models\SrfFormSubmission;
use App\Models\User;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;

class SrfSubmissionSeeder extends Seeder
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

        // Get branches and users
        $branches = Branch::all();
        $users = User::all();

        // Status options
        $statuses = ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'];

        // Sample data for 20 submissions
        for ($i = 1; $i <= 20; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 90))->subHours(rand(1, 23));
            $submittedAt = $statuses[array_rand(['submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])] !== 'draft' 
                ? $startedAt->copy()->addMinutes(rand(15, 120)) 
                : null;

            $status = $statuses[array_rand($statuses)];
            $branch = $branches->random();
            $user = $users->random();
            $reviewedBy = in_array($status, ['under_review', 'approved', 'rejected', 'in_progress', 'completed']) 
                ? $users->random() 
                : null;

            SrfFormSubmission::create([
                'srf_form_id' => $srfForm->id,
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'submission_token' => 'srf_' . uniqid() . '_' . time(),
                'status' => $status,
                'submission_data' => [
                    'customer_name' => fake()->name(),
                    'customer_phone' => fake()->phoneNumber(),
                    'customer_email' => fake()->email(),
                    'service_type' => fake()->randomElement(['deposit', 'withdrawal', 'transfer', 'account_opening', 'other']),
                    'service_category' => fake()->randomElement(['banking', 'investment', 'loan', 'other']),
                    'service_amount' => rand(100, 100000),
                    'service_currency' => 'MYR',
                    'urgency_level' => fake()->randomElement(['low', 'medium', 'high']),
                ],
                'field_responses' => [
                    'customer_name' => fake()->name(),
                    'customer_email' => fake()->email(),
                    'service_type' => fake()->randomElement(['deposit', 'withdrawal', 'transfer']),
                    'service_amount' => (string)rand(100, 100000),
                ],
                'file_uploads' => rand(0, 1) ? [
                    [
                        'name' => 'supporting_document.pdf',
                        'path' => 'uploads/srf/doc_' . uniqid() . '.pdf',
                        'size' => rand(100000, 5000000),
                    ]
                ] : null,
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'session_id' => 'session_' . uniqid(),
                'started_at' => $startedAt,
                'submitted_at' => $submittedAt,
                'last_modified_at' => $submittedAt ? $submittedAt->copy()->addMinutes(rand(1, 30)) : null,
                'reviewed_by' => $reviewedBy?->id,
                'reviewed_at' => $reviewedBy ? $submittedAt?->copy()->addHours(rand(1, 72)) : null,
                'review_notes' => in_array($status, ['approved', 'rejected']) ? fake()->sentence() : null,
                'rejection_reason' => $status === 'rejected' ? fake()->paragraph() : null,
                'audit_trail' => [
                    [
                        'action' => 'created',
                        'timestamp' => $startedAt->toISOString(),
                    ],
                    ...($submittedAt ? [[
                        'action' => 'submitted',
                        'timestamp' => $submittedAt->toISOString(),
                    ]] : []),
                ],
                'compliance_checks' => $submittedAt ? [
                    [
                        'check_type' => 'service_eligibility',
                        'result' => 'passed',
                        'timestamp' => $submittedAt->toISOString(),
                    ]
                ] : null,
                'internal_notes' => rand(0, 1) ? fake()->paragraph() : null,
            ]);
        }

        $this->command->info('Created 20 SRF form submissions.');
    }
}
