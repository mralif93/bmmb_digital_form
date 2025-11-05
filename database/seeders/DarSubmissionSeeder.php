<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DarFormSubmission;
use App\Models\DataAccessRequestForm;
use App\Models\User;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;

class DarSubmissionSeeder extends Seeder
{
    use UsesSystemTimezone;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first DAR form
        $darForm = DataAccessRequestForm::first();
        if (!$darForm) {
            $this->command->warn('No DAR form found. Please create a form first before seeding submissions.');
            return;
        }

        // Get branches and users
        $branches = Branch::all();
        $users = User::all();

        // Status options
        $statuses = ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'expired'];

        // Sample data for 20 submissions
        for ($i = 1; $i <= 20; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 90))->subHours(rand(1, 23));
            $submittedAt = $statuses[array_rand(['submitted', 'under_review', 'approved', 'rejected', 'completed', 'expired'])] !== 'draft' 
                ? $startedAt->copy()->addMinutes(rand(15, 120)) 
                : null;

            $status = $statuses[array_rand($statuses)];
            $branch = $branches->random();
            $user = $users->random();
            $reviewedBy = in_array($status, ['under_review', 'approved', 'rejected', 'completed']) 
                ? $users->random() 
                : null;

            DarFormSubmission::create([
                'dar_form_id' => $darForm->id,
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'submission_token' => 'dar_' . uniqid() . '_' . time(),
                'status' => $status,
                'submission_data' => [
                    'requester_name' => fake()->name(),
                    'requester_phone' => fake()->phoneNumber(),
                    'requester_email' => fake()->email(),
                    'data_subject_name' => fake()->name(),
                    'request_type' => fake()->randomElement(['full_access', 'partial_access', 'specific_data']),
                    'data_categories' => ['personal_info', 'financial_data'],
                    'urgency_level' => fake()->randomElement(['low', 'medium', 'high']),
                ],
                'field_responses' => [
                    'requester_name' => fake()->name(),
                    'requester_email' => fake()->email(),
                    'data_subject_name' => fake()->name(),
                    'request_type' => fake()->randomElement(['full_access', 'partial_access', 'specific_data']),
                ],
                'file_uploads' => rand(0, 1) ? [
                    [
                        'name' => 'authorization_document.pdf',
                        'path' => 'uploads/dar/auth_' . uniqid() . '.pdf',
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
                        'check_type' => 'privacy_compliance',
                        'result' => 'passed',
                        'timestamp' => $submittedAt->toISOString(),
                    ]
                ] : null,
                'internal_notes' => rand(0, 1) ? fake()->paragraph() : null,
            ]);
        }

        $this->command->info('Created 20 DAR form submissions.');
    }
}
