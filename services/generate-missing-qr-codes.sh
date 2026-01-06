#!/bin/bash
# Simple script to generate missing QR codes for branches

echo "Generating QR codes for branches without them..."

php artisan tinker << 'EOF'
// Get all branches
$branches = App\Models\Branch::all();
$created = 0;
$skipped = 0;

foreach ($branches as $branch) {
    // Check if QR code exists
    $existingQr = App\Models\QrCode::where('branch_id', $branch->id)
        ->where('type', 'branch')
        ->first();
    
    if ($existingQr) {
        $skipped++;
        continue;
    }
    
    // Generate QR code
    $token = bin2hex(random_bytes(16));
    $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code, 'token' => $token]);
    $qr = SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->margin(2)->generate($url);
    $fileName = 'qr_' . time() . '_' . uniqid() . '.png';
    Storage::disk('public')->put('qr-codes/' . $fileName, $qr);
    
    $settings = Cache::get('system_settings', []);
    $expMin = (int) ($settings['qr_code_expiration_minutes'] ?? 60);
    
    App\Models\QrCode::create([
        'name' => 'Branch QR - ' . $branch->branch_name,
        'type' => 'branch',
        'content' => $url,
        'branch_id' => $branch->id,
        'qr_code_image' => $fileName,
        'status' => 'active',
        'size' => 300,
        'format' => 'png',
        'created_by' => null,
        'last_regenerated_at' => now(),
        'expires_at' => now()->addMinutes($expMin),
        'validation_token' => $token,
    ]);
    
    echo "Created QR for: " . $branch->branch_name . "\n";
    $created++;
}

echo "\nSummary:\n";
echo "Created: $created\n";
echo "Skipped: $skipped\n";
EOF

echo "Done!"
