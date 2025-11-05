<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormManagementController extends Controller
{
    use LogsAuditTrail;
    private $formConfig = [
        'raf' => [
            'model' => RemittanceApplicationForm::class,
            'title' => 'Remittance Application Form',
            'icon' => 'bx-money',
            'table' => 'remittance_application_forms',
            'number_field' => 'application_number',
            'number_prefix' => 'RAF',
        ],
        'dar' => [
            'model' => DataAccessRequestForm::class,
            'title' => 'Data Access Request Form',
            'icon' => 'bx-data',
            'table' => 'data_access_request_forms',
            'number_field' => 'request_number',
            'number_prefix' => 'DAR',
        ],
        'dcr' => [
            'model' => DataCorrectionRequestForm::class,
            'title' => 'Data Correction Request Form',
            'icon' => 'bx-edit',
            'table' => 'data_correction_request_forms',
            'number_field' => 'request_number',
            'number_prefix' => 'DCR',
        ],
        'srf' => [
            'model' => ServiceRequestForm::class,
            'title' => 'Service Request Form',
            'icon' => 'bx-cog',
            'table' => 'service_request_forms',
            'number_field' => 'request_number',
            'number_prefix' => 'SRF',
        ],
    ];

    /**
     * Display a listing of the forms by type.
     */
    public function index($type)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $model = $config['model'];
        
        $forms = $model::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.forms.index', compact('forms', 'type', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($type)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $users = User::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.forms.create', compact('type', 'config', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $type)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $model = $config['model'];

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Generate unique number
        $year = date('Y');
        
        // Get all forms for this year and find the highest sequence
        $allForms = $model::where($config['number_field'], 'like', $config['number_prefix'] . '-' . $year . '-%')->get();
        $maxSequence = 0;
        
        foreach ($allForms as $form) {
            if (preg_match('/' . preg_quote($config['number_prefix']) . '-' . $year . '-(\d+)$/', $form->{$config['number_field']}, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
            }
        }
        
        $sequence = $maxSequence + 1;
        $number = $config['number_prefix'] . '-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness by checking if the number already exists
        while ($model::where($config['number_field'], $number)->exists()) {
            $sequence++;
            $number = $config['number_prefix'] . '-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        }

        $formData = [
            'user_id' => $validated['user_id'],
            $config['number_field'] => $number,
            'status' => 'draft',
        ];

        // Add type-specific defaults
        if ($type === 'srf') {
            $formData['service_type'] = 'deposit';
        }

        $form = $model::create($formData);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created {$config['title']}: {$form->{$config['number_field']}}",
            modelType: $config['model'],
            modelId: $form->id,
            newValues: $form->toArray()
        );

        return redirect()->route('admin.forms.show', [$type, $form->id])
            ->with('success', $config['title'] . ' created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($type, $id)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $model = $config['model'];
        
        $form = $model::with('user')->findOrFail($id);

        return view('admin.forms.show', compact('form', 'type', 'config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($type, $id)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $model = $config['model'];
        
        $form = $model::findOrFail($id);
        $users = User::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.forms.edit', compact('form', 'type', 'config', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $type, $id)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $model = $config['model'];
        
        $form = $model::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed,in_progress,cancelled,expired,partially_approved',
        ]);

        $oldValues = $form->toArray();
        $form->update($validated);
        $form->refresh();

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated {$config['title']}: {$form->{$config['number_field']}}",
            modelType: $config['model'],
            modelId: $form->id,
            oldValues: $oldValues,
            newValues: $form->toArray()
        );

        return redirect()->route('admin.forms.show', [$type, $form->id])
            ->with('success', $config['title'] . ' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($type, $id)
    {
        if (!isset($this->formConfig[$type])) {
            abort(404);
        }

        $config = $this->formConfig[$type];
        $model = $config['model'];
        
        $form = $model::findOrFail($id);
        $oldValues = $form->toArray();
        $formNumber = $form->{$config['number_field']};
        $formId = $form->id;
        
        $form->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted {$config['title']}: {$formNumber}",
            modelType: $config['model'],
            modelId: $formId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.forms.index', $type)
            ->with('success', $config['title'] . ' deleted successfully!');
    }
}

