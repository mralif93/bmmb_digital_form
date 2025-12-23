<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\QrCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class GenerateBranchQrCodes extends Command
{
    protected $signature = 'branches:generate-qr-codes 
                            {--reset : Delete all existing QR codes first}';

    protected $description = 'Generate QR codes for all branches';

    public function handle()
    {
        $this->info('Generating QR codes for branches...');
        $this->newLine();

        $reset = $this->option('reset');

        // Delete existing QR codes if reset flag is set
        if ($reset) {
            $this->warn('Resetting all QR codes...');
            $qrCodes = QrCode::where('type', 'branch')->get();

            foreach ($qrCodes as $qrCode) {
                if ($qrCode->qr_code_image) {
                    Storage::disk('public')->delete('qr-codes/' . $qrCode->qr_code_image);
                }
            }

            $count = QrCode::where('type', 'branch')->delete();
            $this->info("✓ Deleted {$count} existing QR codes");
            $this->newLine();
        }

        // Get all branches
        $branches = Branch::all();
        $this->info("Found {$branches->count()} branches");
        $this->newLine();

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($branches as $branch) {
            try {
                // Check if QR code already exists
                $existingQr = QrCode::where('branch_id', $branch->id)
                    ->where('type', 'branch')
                    ->first();

                if ($existingQr) {
                    $skipped++;
                    continue;
                }

                // Generate validation token
                $validationToken = bin2hex(random_bytes(16));

                // Generate QR code URL
                $params = [
                    'tiAgentCode' => $branch->ti_agent_code,
                    'token' => $validationToken
                ];
                $qrContent = route('public.branch', $params);

                // Generate QR code image
                $qrCodeImage = QrCodeGenerator::format('png')
                    ->size(300)
                    ->margin(2)
                    ->generate($qrContent);

                // Save QR code image
                $fileName = 'qr_' . time() . '_' . uniqid() . '.png';
                $filePath = 'qr-codes/' . $fileName;
                Storage::disk('public')->put($filePath, $qrCodeImage);

                // Get expiration minutes
                $settings = Cache::get('system_settings', []);
                $expirationMinutes = (int) ($settings['qr_code_expiration_minutes'] ?? 60);

                // Create QR code record
                QrCode::create([
                    'name' => 'Branch QR - ' . $branch->branch_name,
                    'type' => 'branch',
                    'content' => $qrContent,
                    'branch_id' => $branch->id,
                    'qr_code_image' => $fileName,
                    'status' => 'active',
                    'size' => 300,
                    'format' => 'png',
                    'created_by' => null,
                    'last_regenerated_at' => now(),
                    'expires_at' => now()->addMinutes($expirationMinutes),
                    'validation_token' => $validationToken,
                ]);

                $created++;
                $this->line("  <fg=green>✓</> Created QR for: {$branch->branch_name}");

            } catch (\Exception $e) {
                $errors++;
                $this->error("  ✗ Error for {$branch->branch_name}: " . $e->getMessage());
                Log::error('QR code generation error', [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->branch_name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->newLine();
        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Skipped', $skipped],
            ['Errors', $errors],
        ]);

        if ($errors > 0) {
            $this->newLine();
            $this->warn("Check storage/logs/laravel.log for error details");
        }

        $this->newLine();
        $this->info('✓ Done!');

        return Command::SUCCESS;
    }
}
