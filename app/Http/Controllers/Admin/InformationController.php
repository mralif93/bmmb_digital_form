<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Branch;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\FormSubmission;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InformationController extends Controller
{
    /**
     * Display the project information dashboard.
     */
    public function index()
    {
        // System Information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => DB::getDriverName(),
            'database_name' => DB::connection()->getDatabaseName(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_os' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
        ];

        // Database Statistics
        $dbStats = [
            'total_tables' => $this->getTableCount(),
            'total_records' => $this->getTotalRecords(),
            'database_size' => $this->getDatabaseSize(),
        ];

        // Project Statistics
        $projectStats = [
            'total_forms' => Form::count(),
            'total_form_sections' => FormSection::count(),
            'total_form_fields' => FormField::count(),
            'total_submissions' => FormSubmission::count(),
            'total_users' => User::count(),
            'total_branches' => Branch::count(),
            'total_qr_codes' => QrCode::count(),
            'total_audit_trails' => AuditTrail::count(),
            'active_forms' => Form::where('status', 'active')->count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_submissions' => FormSubmission::whereIn('status', ['submitted', 'under_review'])->count(),
        ];

        // Storage Information
        $storageInfo = [
            'storage_path' => storage_path(),
            'public_path' => public_path(),
            'storage_used' => $this->getStorageSize(),
        ];

        // Recent Activity (Last 10 audit trails)
        $recentActivity = AuditTrail::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Form Type Breakdown
        $formTypeBreakdown = [
            'raf' => [
                'forms' => Form::where('slug', 'raf')->count(),
                'submissions' => FormSubmission::whereHas('form', function($q) { $q->where('slug', 'raf'); })->count(),
            ],
            'dar' => [
                'forms' => Form::where('slug', 'dar')->count(),
                'submissions' => FormSubmission::whereHas('form', function($q) { $q->where('slug', 'dar'); })->count(),
            ],
            'dcr' => [
                'forms' => Form::where('slug', 'dcr')->count(),
                'submissions' => FormSubmission::whereHas('form', function($q) { $q->where('slug', 'dcr'); })->count(),
            ],
            'srf' => [
                'forms' => Form::where('slug', 'srf')->count(),
                'submissions' => FormSubmission::whereHas('form', function($q) { $q->where('slug', 'srf'); })->count(),
            ],
        ];

        // Submission Status Breakdown
        $submissionStatusBreakdown = [
            'draft' => FormSubmission::where('status', 'draft')->count(),
            'submitted' => FormSubmission::where('status', 'submitted')->count(),
            'pending_process' => FormSubmission::where('status', 'pending_process')->count(),
            'under_review' => FormSubmission::where('status', 'under_review')->count(),
            'approved' => FormSubmission::where('status', 'approved')->count(),
            'rejected' => FormSubmission::where('status', 'rejected')->count(),
            'completed' => FormSubmission::where('status', 'completed')->count(),
            'expired' => FormSubmission::where('status', 'expired')->count(),
            'in_progress' => FormSubmission::where('status', 'in_progress')->count(),
            'cancelled' => FormSubmission::where('status', 'cancelled')->count(),
        ];

        return view('admin.information', compact(
            'systemInfo',
            'dbStats',
            'projectStats',
            'storageInfo',
            'recentActivity',
            'formTypeBreakdown',
            'submissionStatusBreakdown'
        ));
    }

    /**
     * Get total number of tables in database.
     */
    private function getTableCount(): int
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                return count($tables);
            } elseif ($driver === 'mysql') {
                $database = DB::connection()->getDatabaseName();
                $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [$database]);
                return $tables[0]->count ?? 0;
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total records across all main tables.
     */
    private function getTotalRecords(): int
    {
        try {
            return Form::count() +
                   FormSubmission::count() +
                   User::count() +
                   Branch::count() +
                   QrCode::count() +
                   AuditTrail::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get database size.
     */
    private function getDatabaseSize(): string
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $path = database_path('database.sqlite');
                if (File::exists($path)) {
                    $size = File::size($path);
                    return $this->formatBytes($size);
                }
            } elseif ($driver === 'mysql') {
                $database = DB::connection()->getDatabaseName();
                $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.tables WHERE table_schema = ?", [$database]);
                if (!empty($result) && isset($result[0]->size)) {
                    return $result[0]->size . ' MB';
                }
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get storage directory size.
     */
    private function getStorageSize(): string
    {
        try {
            $path = storage_path('app');
            if (File::exists($path)) {
                $size = $this->getDirectorySize($path);
                return $this->formatBytes($size);
            }
            return '0 B';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Calculate directory size recursively.
     */
    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        foreach (File::allFiles($directory) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

