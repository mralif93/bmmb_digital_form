<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\State;
use App\Models\Region;
use App\Models\QrCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use PDO;

class SyncMapBranchesFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:sync-branches 
                            {--dry-run : Show what would be synced without making changes}
                            {--include-regions : Also sync regions}
                            {--include-states : Also sync states}
                            {--all : Sync regions, states, and branches}
                            {--skip-qr-codes : Skip QR code generation for branches}
                            {--reset-qr-codes : Delete all existing QR codes and regenerate fresh ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync branches, states, and regions from MAP database to eForm';

    private PDO $mapDb;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting MAP data sync...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $syncAll = $this->option('all');
        $syncRegions = $syncAll || $this->option('include-regions');
        $syncStates = $syncAll || $this->option('include-states');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $skipQrCodes = $this->option('skip-qr-codes');
        $resetQrCodes = $this->option('reset-qr-codes');

        // Reset QR codes if flag is set
        if ($resetQrCodes && !$dryRun) {
            $this->resetAllQrCodes();
        }

        // Connect to MAP database
        $mapDbPath = $this->getMapDatabasePath();

        if (!file_exists($mapDbPath)) {
            $this->error("MAP database not found at: {$mapDbPath}");
            $this->info('Please set MAP_DATABASE_PATH in .env or ensure the path is correct.');
            return Command::FAILURE;
        }

        try {
            $this->mapDb = new PDO("sqlite:{$mapDbPath}");
            $this->mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            $this->error("Failed to connect to MAP database: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Sync in order: Regions → States → Branches
        if ($syncRegions) {
            $this->syncRegions($dryRun);
            $this->newLine();
        }

        if ($syncStates) {
            $this->syncStates($dryRun);
            $this->newLine();
        }

        // If resetting QR codes, force QR generation (ignore skip flag)
        $generateQrCodes = $resetQrCodes ? true : !$skipQrCodes;
        $this->syncBranches($dryRun, $generateQrCodes);

        $this->newLine();
        $this->info('✓ Sync completed!');

        return Command::SUCCESS;
    }

    /**
     * Sync regions from MAP
     */
    private function syncRegions(bool $dryRun): void
    {
        $this->info('Syncing Regions...');

        $stmt = $this->mapDb->query("SELECT id, name FROM Application_region ORDER BY id");
        $mapRegions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($mapRegions as $mapRegion) {
            $existing = Region::find($mapRegion['id']);

            if (!$existing) {
                if (!$dryRun) {
                    Region::create([
                        'id' => $mapRegion['id'],
                        'name' => $mapRegion['name'],
                        'links' => '', // Empty for now, MAP doesn't have this field
                    ]);
                }
                $created++;
                $this->line("  <fg=green>+</> Created: {$mapRegion['name']}");
            } elseif ($existing->name !== $mapRegion['name']) {
                if (!$dryRun) {
                    $existing->update(['name' => $mapRegion['name']]);
                }
                $updated++;
                $this->line("  <fg=yellow>~</> Updated: {$mapRegion['name']}");
            } else {
                $skipped++;
            }
        }

        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
        ]);
    }

    /**
     * Sync states from MAP
     */
    private function syncStates(bool $dryRun): void
    {
        $this->info('Syncing States...');

        $stmt = $this->mapDb->query("SELECT id, name FROM Application_state ORDER BY id");
        $mapStates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($mapStates as $mapState) {
            $existing = State::find($mapState['id']);

            if (!$existing) {
                if (!$dryRun) {
                    State::create([
                        'id' => $mapState['id'],
                        'name' => $mapState['name'],
                    ]);
                }
                $created++;
                $this->line("  <fg=green>+</> Created: {$mapState['name']}");
            } elseif ($existing->name !== $mapState['name']) {
                if (!$dryRun) {
                    $existing->update(['name' => $mapState['name']]);
                }
                $updated++;
                $this->line("  <fg=yellow>~</> Updated: {$mapState['name']}");
            } else {
                $skipped++;
            }
        }

        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
        ]);
    }

    /**
     * Sync branches from MAP
     */
    private function syncBranches(bool $dryRun, bool $generateQrCodes = true): void
    {
        $this->info('Syncing Branches...');

        $stmt = $this->mapDb->query("
            SELECT 
                id,
                title as branch_name,
                ti_agent_code,
                address,
                email,
                state_id,
                region_id,
                weekend_start_day
            FROM Application_branch 
            ORDER BY id
        ");
        $mapBranches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $qrCreated = 0;
        $qrSkipped = 0;

        foreach ($mapBranches as $mapBranch) {
            try {
                // Find by ti_agent_code (unique identifier) instead of id
                $existing = Branch::withTrashed()
                    ->where('ti_agent_code', $mapBranch['ti_agent_code'])
                    ->first();

                $data = [
                    'id' => $mapBranch['id'], // Preserve MAP ID
                    'branch_name' => $mapBranch['branch_name'],
                    'ti_agent_code' => $mapBranch['ti_agent_code'],
                    'address' => $mapBranch['address'],
                    'email' => $mapBranch['email'],
                    'state_id' => $mapBranch['state_id'],
                    'region_id' => $mapBranch['region_id'],
                    'weekend_start_day' => $mapBranch['weekend_start_day'],
                ];

                if (!$existing) {
                    if (!$dryRun) {
                        Branch::create($data);
                    }
                    $created++;
                    $this->line("  <fg=green>+</> Created: {$mapBranch['branch_name']}");
                } else {
                    // For updates, don't change the ID to avoid foreign key constraint violations
                    $updateData = [
                        'branch_name' => $mapBranch['branch_name'],
                        'ti_agent_code' => $mapBranch['ti_agent_code'],
                        'address' => $mapBranch['address'],
                        'email' => $mapBranch['email'],
                        'state_id' => $mapBranch['state_id'],
                        'region_id' => $mapBranch['region_id'],
                        'weekend_start_day' => $mapBranch['weekend_start_day'],
                    ];

                    // Check if anything changed
                    $hasChanges = false;
                    foreach ($updateData as $key => $value) {
                        if ($existing->$key != $value) {
                            $hasChanges = true;
                            break;
                        }
                    }

                    if ($hasChanges) {
                        if (!$dryRun) {
                            // Restore if trashed
                            if ($existing->trashed()) {
                                $existing->restore();
                            }
                            $existing->update($updateData);
                        }
                        $updated++;
                        $this->line("  <fg=yellow>~</> Updated: {$mapBranch['branch_name']}");
                    } else {
                        $skipped++;
                    }
                }

                // Generate QR code for this branch
                // Process all branches: created, updated, AND skipped
                if ($generateQrCodes) {
                    // Use the actual eForm branch ID
                    // For existing branches, use their current ID
                    // For new branches (not in dry-run), use the MAP ID
                    if ($existing) {
                        $branchId = $existing->id;
                    } else {
                        // New branch - in dry-run, we can't get the ID, so skip QR generation
                        // In actual run, the branch will be created with MAP ID
                        if ($dryRun) {
                            // Can't generate QR for non-existent branch in dry-run
                            continue;
                        }
                        $branchId = $mapBranch['id'];
                    }

                    $qrResult = $this->generateQrCodeForBranch($branchId, $mapBranch['branch_name'], $dryRun);
                    if ($qrResult === 'created') {
                        $qrCreated++;
                    } elseif ($qrResult === 'skipped') {
                        $qrSkipped++;
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error syncing {$mapBranch['branch_name']}: " . $e->getMessage());
                Log::error('MAP branch sync error', [
                    'branch' => $mapBranch['branch_name'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
            ['Errors', $errors],
        ]);

        // Show QR code statistics if generated
        if ($generateQrCodes) {
            $this->newLine();
            $this->info('QR Code Generation:');
            $this->table(['Action', 'Count'], [
                ['QR Codes Created', $qrCreated],
                ['QR Codes Skipped', $qrSkipped],
            ]);
        }
    }

    /**
     * Reset all QR codes (delete existing ones)
     */
    private function resetAllQrCodes(): void
    {
        $this->warn('Resetting all QR codes...');

        // Get all branch QR codes
        $qrCodes = QrCode::where('type', 'branch')->get();
        $count = $qrCodes->count();

        if ($count === 0) {
            $this->info('No existing QR codes to delete.');
            return;
        }

        // Delete QR code image files
        foreach ($qrCodes as $qrCode) {
            if ($qrCode->qr_code_image) {
                $filePath = 'qr-codes/' . $qrCode->qr_code_image;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        // Delete database records
        QrCode::where('type', 'branch')->delete();

        $this->info("✓ Deleted {$count} existing QR codes");
        $this->newLine();
    }

    /**
     * Generate QR code for a branch
     *
     * @param int $branchId
     * @param string $branchName
     * @param bool $dryRun
     * @return string 'created', 'skipped', or 'error'
     */
    private function generateQrCodeForBranch(int $branchId, string $branchName, bool $dryRun): string
    {
        try {
            // Check if QR code already exists for this branch
            $existingQrCode = QrCode::where('branch_id', $branchId)
                ->where('type', 'branch')
                ->first();

            if ($existingQrCode) {
                return 'skipped';
            }

            if ($dryRun) {
                return 'created';
            }

            // Get the branch to retrieve ti_agent_code
            $branch = Branch::find($branchId);
            if (!$branch) {
                return 'error';
            }

            // Generate validation token
            $validationToken = bin2hex(random_bytes(16));

            // Generate QR code content (branch URL with token)
            $params = [
                'tiAgentCode' => $branch->ti_agent_code,
                'token' => $validationToken
            ];
            $qrContent = route('public.branch', $params);

            // Default settings
            $size = 300;
            $format = 'png';

            // Generate QR code image
            $qrCodeImage = QrCodeGenerator::format($format)
                ->size($size)
                ->margin(2)
                ->generate($qrContent);

            // Save QR code image
            $fileName = 'qr_' . time() . '_' . uniqid() . '.' . $format;
            $filePath = 'qr-codes/' . $fileName;
            Storage::disk('public')->put($filePath, $qrCodeImage);

            // Get expiration minutes from settings
            $expirationMinutes = $this->getQrCodeExpirationMinutes();

            // Create QR code record
            QrCode::create([
                'name' => 'Branch QR - ' . $branchName,
                'type' => 'branch',
                'content' => $qrContent,
                'branch_id' => $branchId,
                'qr_code_image' => $fileName,
                'status' => 'active',
                'size' => $size,
                'format' => $format,
                'created_by' => null, // System-generated
                'last_regenerated_at' => now(),
                'expires_at' => now()->addMinutes($expirationMinutes),
                'validation_token' => $validationToken,
            ]);

            return 'created';
        } catch (\Exception $e) {
            Log::error('QR code generation error', [
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'error' => $e->getMessage()
            ]);
            return 'error';
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

    /**
     * Get MAP database path
     */
    private function getMapDatabasePath(): string
    {
        $envPath = env('MAP_DATABASE_PATH');
        if ($envPath) {
            return $envPath;
        }

        return config(
            'map.database_path',
            base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3')
        );
    }
}
