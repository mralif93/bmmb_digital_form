<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Form;
use App\Models\QrCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeSeeder extends Seeder
{
    /**
     * Generate QR codes for all branches and forms.
     */
    public function run(): void
    {
        $this->command->info('Generating QR codes for branches...');

        // Clear existing QR codes to ensure clean state and remove invalid types
        QrCode::truncate();

        $branches = Branch::whereNotNull('ti_agent_code')
            ->where('ti_agent_code', '!=', '')
            ->get();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Run BranchSeeder first.');
            return;
        }

        $this->command->info("Processing {$branches->count()} branches...");

        $count = 0;
        $errors = 0;

        foreach ($branches as $branch) {
            try {
                // Build the URL for the branch landing page
                // We use the route helper to ensure the correct URL structure
                // Ideally this points to the branch-specific landing page where users can select forms
                $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code]);

                // Generate QR code name
                $qrName = "Branch QR - {$branch->branch_name}";

                // Generate validation token
                $validationToken = Str::random(32);

                // Create or update QR code record
                QrCode::create([
                    'branch_id' => $branch->id,
                    'type' => 'branch',
                    'name' => $qrName,
                    'content' => $url,
                    'qr_code_image' => null,
                    'status' => 'active',
                    'size' => 300,
                    'format' => 'svg',
                    'validation_token' => $validationToken,
                    'last_regenerated_at' => now(),
                    'created_by' => 1, // Default admin user if available, or null
                ]);

                $count++;

            } catch (\Exception $e) {
                $errors++;
                Log::warning('QR code generation error', [
                    'branch' => $branch->ti_agent_code,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->command->info("✓ Generated {$count} QR codes" . ($errors > 0 ? " ({$errors} errors)" : ""));
    }
}
