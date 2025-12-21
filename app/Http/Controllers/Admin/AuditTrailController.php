<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    /**
     * Display a listing of audit trails.
     */
    public function index(Request $request)
    {
        $query = AuditTrail::with('user')->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditTrails = $query->paginate(20)->withQueryString();

        // Get unique actions for filter
        $actions = AuditTrail::distinct()->pluck('action')->sort()->values();
        
        // Get unique model types for filter
        $modelTypes = AuditTrail::distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();
        
        // Get users for filter
        $users = User::whereHas('auditTrails')->orderBy('first_name')->get();

        return view('admin.audit-trails.index', compact('auditTrails', 'actions', 'modelTypes', 'users'));
    }

    /**
     * Display the specified audit trail.
     */
    public function show(AuditTrail $auditTrail)
    {
        $auditTrail->load('user');
        return view('admin.audit-trails.show', compact('auditTrail'));
    }
}