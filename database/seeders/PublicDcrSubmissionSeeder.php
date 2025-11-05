<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DcrFormSubmission;
use App\Models\DataCorrectionRequestForm;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicDcrSubmissionSeeder extends Seeder
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

        // Get branches for branch linking
        $branches = Branch::all();

        // Sample data for 5 public submissions
        for ($i = 1; $i <= 5; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 30))->subHours(rand(1, 23));
            $submittedAt = $startedAt->copy()->addMinutes(rand(10, 60));
            
            // Randomly assign branch (some submissions may be from QR code)
            $branch = $branches->isNotEmpty() && rand(0, 1) ? $branches->random() : null;

            // Generate realistic form data
            $requesterName = fake()->name();
            $requesterEmail = fake()->email();
            $requesterPhone = fake()->phoneNumber();
            $dataSubjectName = fake()->name();
            
            $idTypes = ['passport', 'national_id', 'drivers_license', 'other'];
            $correctionTypes = ['personal_info', 'contact_info', 'financial_data', 'transaction_record', 'account_details', 'other'];
            $priorities = ['low', 'medium', 'high', 'urgent'];
            
            $selectedCorrectionTypes = array_rand($correctionTypes, rand(1, 3));
            if (!is_array($selectedCorrectionTypes)) {
                $selectedCorrectionTypes = [$selectedCorrectionTypes];
            }
            $selectedTypes = array_map(fn($idx) => $correctionTypes[$idx], $selectedCorrectionTypes);

            DcrFormSubmission::create([
                'dcr_form_id' => $dcrForm->id,
                'user_id' => null, // Public submission (no user login)
                'branch_id' => $branch?->id,
                'submission_token' => Str::random(32) . '-' . time() . '_' . $i,
                'status' => 'submitted',
                'submission_data' => [
                    'requester_name' => $requesterName,
                    'requester_email' => $requesterEmail,
                    'requester_phone' => $requesterPhone,
                    'requester_address' => fake()->address(),
                    'data_subject_name' => $dataSubjectName,
                    'data_subject_email' => fake()->email(),
                    'data_subject_phone' => fake()->phoneNumber(),
                    'data_subject_address' => fake()->address(),
                    'data_subject_id_type' => $idTypes[array_rand($idTypes)],
                    'data_subject_id_number' => fake()->numerify('##########'),
                    'data_subject_id_expiry_date' => fake()->dateTimeBetween('+1 month', '+5 years')->format('Y-m-d'),
                    'correction_type' => $selectedTypes,
                    'incorrect_data_description' => fake()->paragraph(),
                    'correct_data_details' => fake()->paragraph(),
                    'reason_for_correction' => fake()->paragraph(),
                    'supporting_documents' => fake()->sentence(),
                    'priority' => $priorities[array_rand($priorities)],
                    'impact_description' => fake()->paragraph(),
                    'terms_agreement' => '1',
                ],
                'field_responses' => [
                    'requester_name' => $requesterName,
                    'requester_email' => $requesterEmail,
                    'requester_phone' => $requesterPhone,
                    'data_subject_name' => $dataSubjectName,
                    'data_subject_email' => fake()->email(),
                    'data_subject_id_type' => $idTypes[array_rand($idTypes)],
                    'data_subject_id_number' => fake()->numerify('##########'),
                    'correction_type' => implode(', ', $selectedTypes),
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

        $this->command->info('Created 5 public DCR form submissions.');
    }
}

