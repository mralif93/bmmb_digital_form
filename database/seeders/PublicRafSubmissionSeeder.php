<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\RafFormSubmission;
use App\Models\RemittanceApplicationForm;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicRafSubmissionSeeder extends Seeder
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

        // Get branches for branch linking
        $branches = Branch::all();

        // Sample data for 5 public submissions
        for ($i = 1; $i <= 5; $i++) {
            $startedAt = $this->nowInSystemTimezone()->subDays(rand(1, 30))->subHours(rand(1, 23));
            $submittedAt = $startedAt->copy()->addMinutes(rand(10, 60));
            
            // Randomly assign branch (some submissions may be from QR code)
            $branch = $branches->isNotEmpty() && rand(0, 1) ? $branches->random() : null;

            // Generate realistic form data
            $applicantName = fake()->name();
            $applicantEmail = fake()->email();
            $applicantPhone = fake()->phoneNumber();
            $beneficiaryName = fake()->name();
            
            $remittanceAmount = rand(100, 50000);
            $currencies = ['USD', 'EUR', 'GBP', 'MYR', 'SGD', 'AUD'];
            $currency = $currencies[array_rand($currencies)];
            
            $purposes = ['family_support', 'education', 'medical', 'business', 'investment', 'other'];
            $frequencies = ['one_time', 'monthly', 'quarterly', 'annually'];
            $paymentMethods = ['bank_transfer', 'credit_card', 'debit_card', 'cash', 'other'];
            
            $idTypes = ['passport', 'national_id', 'drivers_license', 'other'];
            $relationships = ['spouse', 'parent', 'child', 'sibling', 'relative', 'friend', 'business_partner', 'other'];
            
            $countries = ['US', 'CA', 'GB', 'AU', 'DE', 'FR', 'JP', 'SG', 'MY'];

            RafFormSubmission::create([
                'raf_form_id' => $rafForm->id,
                'user_id' => null, // Public submission (no user login)
                'branch_id' => $branch?->id,
                'submission_token' => Str::random(32) . '-' . time() . '_' . $i,
                'status' => 'submitted',
                'submission_data' => [
                    'applicant_name' => $applicantName,
                    'applicant_email' => $applicantEmail,
                    'applicant_phone' => $applicantPhone,
                    'applicant_id_type' => $idTypes[array_rand($idTypes)],
                    'applicant_id_number' => fake()->numerify('##########'),
                    'applicant_id_expiry_date' => fake()->dateTimeBetween('+1 month', '+5 years')->format('Y-m-d'),
                    'applicant_address' => fake()->streetAddress(),
                    'applicant_city' => fake()->city(),
                    'applicant_state' => fake()->state(),
                    'applicant_postal_code' => fake()->postcode(),
                    'applicant_country' => $countries[array_rand($countries)],
                    'remittance_amount' => $remittanceAmount,
                    'remittance_currency' => $currency,
                    'remittance_purpose' => $purposes[array_rand($purposes)],
                    'remittance_frequency' => $frequencies[array_rand($frequencies)],
                    'remittance_purpose_description' => fake()->sentence(),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'payment_source' => fake()->company() . ' Bank',
                    'beneficiary_name' => $beneficiaryName,
                    'beneficiary_relationship' => $relationships[array_rand($relationships)],
                    'beneficiary_phone' => fake()->phoneNumber(),
                    'beneficiary_email' => fake()->email(),
                    'beneficiary_address' => fake()->streetAddress(),
                    'beneficiary_city' => fake()->city(),
                    'beneficiary_state' => fake()->state(),
                    'beneficiary_postal_code' => fake()->postcode(),
                    'beneficiary_country' => $countries[array_rand($countries)],
                    'terms_agreement' => '1',
                ],
                'field_responses' => [
                    'applicant_name' => $applicantName,
                    'applicant_email' => $applicantEmail,
                    'applicant_phone' => $applicantPhone,
                    'applicant_id_type' => $idTypes[array_rand($idTypes)],
                    'applicant_id_number' => fake()->numerify('##########'),
                    'applicant_address' => fake()->streetAddress(),
                    'applicant_city' => fake()->city(),
                    'applicant_state' => fake()->state(),
                    'applicant_postal_code' => fake()->postcode(),
                    'applicant_country' => $countries[array_rand($countries)],
                    'remittance_amount' => (string)$remittanceAmount,
                    'remittance_currency' => $currency,
                    'remittance_purpose' => $purposes[array_rand($purposes)],
                    'remittance_frequency' => $frequencies[array_rand($frequencies)],
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'beneficiary_name' => $beneficiaryName,
                    'beneficiary_relationship' => $relationships[array_rand($relationships)],
                    'beneficiary_country' => $countries[array_rand($countries)],
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

        $this->command->info('Created 5 public RAF form submissions.');
    }
}

