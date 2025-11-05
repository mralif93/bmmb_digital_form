<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DcrFormSubmission;
use App\Models\DataCorrectionRequestForm;
use App\Models\User;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;

class DcrSubmissionSeeder extends Seeder
{
    use UsesSystemTimezone;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first DCR form
        $dcrForm = DataCorrectionRequestForm::first();
        if (!$dcrForm) {
            $this->command->warn('No DCR form found. Please create a form first before seeding submissions.');
            return;
        }

        // Get branches and users
        $branches = Branch::all();
        $users = User::all();

        // Status options
        $statuses = ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'partially_approved'];

        // Sample data for 20 submissions
        for ($i = 1; $i <= 20; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 90))->subHours(rand(1, 23));
            $submittedAt = $statuses[array_rand(['submitted', 'under_review', 'approved', 'rejected', 'completed', 'partially_approved'])] !== 'draft' 
                ? $startedAt->copy()->addMinutes(rand(15, 120)) 
                : null;

            $status = $statuses[array_rand($statuses)];
            $branch = $branches->random();
            $user = $users->random();
            $reviewedBy = in_array($status, ['under_review', 'approved', 'rejected', 'completed', 'partially_approved']) 
                ? $users->random() 
                : null;

            DcrFormSubmission::create([
                'dcr_form_id' => $dcrForm->id,
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'submission_token' => 'dcr_' . uniqid() . '_' . time(),
                'status' => $status,
                'submission_data' => [
                    'requester_name' => fake()->name(),
                    'requester_phone' => fake()->phoneNumber(),
                    'requester_email' => fake()->email(),
                    'data_subject_name' => fake()->name(),
                    'correction_type' => fake()->randomElement(['name_correction', 'address_correction', 'phone_correction', 'email_correction', 'other']),
                    'correction_description' => fake()->sentence(),
                    'incorrect_data_items' => ['old_value' => fake()->word()],
                    'corrected_data_items' => ['new_value' => fake()->word()],
                    'urgency_level' => fake()->randomElement(['low', 'medium', 'high']),
                ],
                'field_responses' => [
                    'requester_name' => fake()->name(),
                    'requester_email' => fake()->email(),
                    'correction_type' => fake()->randomElement(['name_correction', 'address_correction', 'phone_correction']),
                    'correction_description' => fake()->sentence(),
                ],
                'file_uploads' => rand(0, 1) ? [
                    [
                        'name' => 'correction_proof.pdf',
                        'path' => 'uploads/dcr/proof_' . uniqid() . '.pdf',
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
                'review_notes' => in_array($status, ['approved', 'rejected', 'partially_approved']) ? fake()->sentence() : null,
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
                        'check_type' => 'data_verification',
                        'result' => 'passed',
                        'timestamp' => $submittedAt->toISOString(),
                    ]
                ] : null,
                'internal_notes' => rand(0, 1) ? fake()->paragraph() : null,
            ]);
        }

        $this->command->info('Created 20 DCR form submissions.');
    }
}
