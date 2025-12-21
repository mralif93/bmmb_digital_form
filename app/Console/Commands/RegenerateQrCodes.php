<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\QrCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class RegenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr-codes:regenerate {--all : Regenerate all active QR codes, not just expired ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR code images (expired codes by default, or all active codes with --all flag)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $regenerateAll = $this->option('all');
        
        if ($regenerateAll) {
            $this->info('Starting QR code regeneration for ALL active codes...');
            
            // Get all active QR codes
            $qrCodes = QrCode::where('status', 'active')->get();
            $this->info("Found {$qrCodes->count()} active QR codes to regenerate.");
        } else {
            $this->info('Starting QR code regeneration for expired codes...');

            // Get only expired active QR codes (expires_at < now AND status = active)
            $qrCodes = QrCode::where('status', 'active')
                ->where(function($query) {
                    $query->where('expires_at', '<', now())
                          ->orWhereNull('expires_at'); // Also regenerate codes without expiration (legacy)
                })
                ->get();
            $this->info("Found {$qrCodes->count()} expired QR codes to regenerate.");
        }
        
        $total = $qrCodes->count();
        $regenerated = 0;
        $errors = 0;

        foreach ($qrCodes as $qrCode) {
            try {
                // Generate new validation token for security
                $newToken = bin2hex(random_bytes(16));
                
                // Generate QR code content based on type
                $qrContent = $this->generateQrContent(
                    $qrCode->type,
                    $qrCode->content,
                    $qrCode->branch_id,
                    $newToken
                );

                // Delete old QR code image if exists
                if ($qrCode->qr_code_image) {
                    Storage::disk('public')->delete('qr-codes/' . $qrCode->qr_code_image);
                }

                // Generate new QR code image
                $qrCodeImage = QrCodeGenerator::format($qrCode->format)
                    ->size($qrCode->size)
                    ->margin(2)
                    ->generate($qrContent);

                // Save new QR code image
                $fileName = 'qr_' . time() . '_' . uniqid() . '.' . $qrCode->format;
                $filePath = 'qr-codes/' . $fileName;
                Storage::disk('public')->put($filePath, $qrCodeImage);

                // Update QR code record with new expiration and token
                $qrCode->update([
                    'qr_code_image' => $fileName,
                    'content' => $qrContent,
                    'last_regenerated_at' => now(),
                    'expires_at' => now()->addMinutes($this->getQrCodeExpirationMinutes()),
                    'validation_token' => $newToken,
                ]);

                $regenerated++;
                $this->line("✓ Regenerated QR code: {$qrCode->name} (ID: {$qrCode->id})");
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Failed to regenerate QR code: {$qrCode->name} (ID: {$qrCode->id}) - {$e->getMessage()}");
            }
        }

        $this->info("\n=== Regeneration Complete ===");
        $this->info("Total QR codes: {$total}");
        $this->info("Successfully regenerated: {$regenerated}");
        $this->info("Errors: {$errors}");

        return Command::SUCCESS;
    }

    /**
     * Generate QR code content based on type
     */
    private function generateQrContent(string $type, string $content, ?int $branchId = null, ?string $token = null): string
    {
        $type = strtolower($type);
        switch ($type) {
            case 'branch':
                if ($branchId) {
                    $branch = Branch::find($branchId);
                    if ($branch) {
                        $params = ['tiAgentCode' => $branch->ti_agent_code];
                        if ($token) {
                            $params['token'] = $token;
                        }
                        return route('public.branch', $params);
                    }
                }
                return $content;
            case 'url':
                return $content;
            case 'text':
                return $content;
            case 'phone':
                return 'tel:' . $content;
            case 'email':
                return 'mailto:' . $content;
            case 'sms':
                return 'sms:' . $content;
            case 'wifi':
                return 'WIFI:T:' . $content . ';;';
            case 'vcard':
                return $content;
            default:
                return $content;
        }
    }

    /**
     * Get QR code expiration minutes from settings
     */
    private function getQrCodeExpirationMinutes(): int
    {
        $settings = Cache::get('system_settings', []);
        return (int) ($settings['qr_code_expiration_minutes'] ?? 60);
    }
}
