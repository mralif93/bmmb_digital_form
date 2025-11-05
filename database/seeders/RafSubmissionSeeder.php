<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\RafFormSubmission;
use App\Models\RemittanceApplicationForm;
use App\Models\User;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;

class RafSubmissionSeeder extends Seeder
{
    use UsesSystemTimezone;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first RAF form
        $rafForm = RemittanceApplicationForm::first();
        if (!$rafForm) {
            $this->command->warn('No RAF form found. Please create a form first before seeding submissions.');
            return;
        }

        // Get branches and users
        $branches = Branch::all();
        $users = User::all();

        // Status options
        $statuses = ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed'];

        // Sample data for 20 submissions
        for ($i = 1; $i <= 20; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 90))->subHours(rand(1, 23));
            $submittedAt = $statuses[array_rand(['submitted', 'under_review', 'approved', 'rejected', 'completed'])] !== 'draft' 
                ? $startedAt->copy()->addMinutes(rand(15, 120)) 
                : null;

            $status = $statuses[array_rand($statuses)];
            $branch = $branches->random();
            $user = $users->random();
            $reviewedBy = in_array($status, ['under_review', 'approved', 'rejected', 'completed']) 
                ? $users->random() 
                : null;

            RafFormSubmission::create([
                'raf_form_id' => $rafForm->id,
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'submission_token' => 'raf_' . uniqid() . '_' . time(),
                'status' => $status,
                'submission_data' => [
                    'applicant_name' => fake()->name(),
                    'applicant_phone' => fake()->phoneNumber(),
                    'applicant_email' => fake()->email(),
                    'applicant_address' => fake()->address(),
                    'remittance_amount' => rand(100, 50000),
                    'remittance_currency' => 'MYR',
                    'beneficiary_name' => fake()->name(),
                    'beneficiary_country' => fake()->country(),
                ],
                'field_responses' => [
                    'applicant_name' => fake()->name(),
                    'applicant_phone' => fake()->phoneNumber(),
                    'applicant_email' => fake()->email(),
                    'remittance_amount' => (string)rand(100, 50000),
                    'remittance_currency' => 'MYR',
                ],
                'file_uploads' => rand(0, 1) ? [
                    [
                        'name' => 'id_document.pdf',
                        'path' => 'uploads/raf/id_' . uniqid() . '.pdf',
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
                'reviewed_at' => $reviewedBy ? $submittedAt?->copy()->addHours(rand(1, 48)) : null,
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
                        'check_type' => 'aml',
                        'result' => 'passed',
                        'timestamp' => $submittedAt->toISOString(),
                    ]
                ] : null,
                'internal_notes' => rand(0, 1) ? fake()->paragraph() : null,
            ]);
        }

        $this->command->info('Created 20 RAF form submissions.');
    }
}
