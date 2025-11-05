<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DarFormSubmission;
use App\Models\DataAccessRequestForm;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicDarSubmissionSeeder extends Seeder
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
            
            $requestTypes = ['full_access', 'partial_access', 'specific_data'];
            $idTypes = ['passport', 'national_id', 'drivers_license', 'other'];
            $organizationTypes = ['individual', 'company', 'non_profit', 'government'];
            $dataCategories = [
                'personal_info',
                'financial_data',
                'transaction_history',
                'account_details',
                'communication_records',
            ];
            $timeframes = ['last_30_days', 'last_90_days', 'last_6_months', 'last_1_year', 'all_time'];
            $urgencyLevels = ['low', 'medium', 'high'];

            $selectedCategories = array_rand($dataCategories, rand(1, 3));
            if (!is_array($selectedCategories)) {
                $selectedCategories = [$selectedCategories];
            }
            $selectedDataCategories = array_map(fn($idx) => $dataCategories[$idx], $selectedCategories);

            DarFormSubmission::create([
                'dar_form_id' => $darForm->id,
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
                    'organization_name' => fake()->company(),
                    'organization_type' => $organizationTypes[array_rand($organizationTypes)],
                    'organization_address' => fake()->address(),
                    'organization_contact_person' => fake()->name(),
                    'organization_contact_email' => fake()->email(),
                    'organization_contact_phone' => fake()->phoneNumber(),
                    'request_type' => $requestTypes[array_rand($requestTypes)],
                    'requested_data_categories' => $selectedDataCategories,
                    'requested_data_timeframe' => $timeframes[array_rand($timeframes)],
                    'request_purpose' => fake()->sentence(),
                    'request_justification' => fake()->paragraph(),
                    'data_usage_description' => fake()->paragraph(),
                    'urgency_level' => $urgencyLevels[array_rand($urgencyLevels)],
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
                    'organization_name' => fake()->company(),
                    'organization_type' => $organizationTypes[array_rand($organizationTypes)],
                    'request_type' => $requestTypes[array_rand($requestTypes)],
                    'requested_data_categories' => implode(', ', $selectedDataCategories),
                    'requested_data_timeframe' => $timeframes[array_rand($timeframes)],
                    'urgency_level' => $urgencyLevels[array_rand($urgencyLevels)],
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

        $this->command->info('Created 5 public DAR form submissions.');
    }
}

