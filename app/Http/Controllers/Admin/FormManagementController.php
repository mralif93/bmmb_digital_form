<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormManagementController extends Controller
{
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
        $lastForm = $model::where($config['number_field'], 'like', $config['number_prefix'] . '-' . $year . '-%')
            ->orderBy($config['number_field'], 'desc')
            ->first();

        $sequence = 1;
        if ($lastForm) {
            $lastSequence = (int) substr($lastForm->{$config['number_field']}, -6);
            $sequence = $lastSequence + 1;
        }

        $number = $config['number_prefix'] . '-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);

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

        $form->update($validated);

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
        $form->delete();

        return redirect()->route('admin.forms.index', $type)
            ->with('success', $config['title'] . ' deleted successfully!');
    }
}

