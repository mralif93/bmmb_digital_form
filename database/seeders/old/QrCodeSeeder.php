<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\QrCode;
use App\Models\User;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeSeeder extends Seeder
{
    use UsesSystemTimezone;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all branches
        $branches = Branch::all();
        
        // Get first admin user for created_by, or use null
        $adminUser = User::where('role', 'admin')->first();
        
        foreach ($branches as $branch) {
            // Generate validation token
            $validationToken = bin2hex(random_bytes(16));
            
            // Generate QR code content (branch URL with token)
            $qrContent = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code, 'token' => $validationToken]);
            
            // Generate QR code image
            $qrCodeImage = QrCodeGenerator::format('png')
                ->size(300)
                ->margin(2)
                ->generate($qrContent);
            
            // Save QR code image
            $fileName = 'qr_' . time() . '_' . uniqid() . '_' . $branch->ti_agent_code . '.png';
            $filePath = 'qr-codes/' . $fileName;
            Storage::disk('public')->put($filePath, $qrCodeImage);
            
            // Create QR code record
            QrCode::create([
                'name' => $branch->branch_name . ' QR Code',
                'type' => 'branch',
                'content' => $qrContent,
                'branch_id' => $branch->id,
                'qr_code_image' => $fileName,
                'status' => 'active',
                'size' => 300,
                'format' => 'png',
                'created_by' => $adminUser ? $adminUser->id : null,
                'expires_at' => $this->nowInSystemTimezone()->addMinutes($this->getQrCodeExpirationMinutes()),
                'validation_token' => $validationToken,
            ]);
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
