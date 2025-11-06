<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RemittanceApplicationForm;
use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\ServiceRequestForm;
use App\Models\RafFormField;
use App\Models\DarFormField;
use App\Models\DcrFormField;
use App\Models\SrfFormField;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    use LogsAuditTrail;

    private $formModelMap = [
        'raf' => [
            'model' => RemittanceApplicationForm::class,
            'field_model' => RafFormField::class,
            'form_id_field' => 'raf_form_id',
            'title' => 'Remittance Application Form',
        ],
        'dar' => [
            'model' => DataAccessRequestForm::class,
            'field_model' => DarFormField::class,
            'form_id_field' => 'dar_form_id',
            'title' => 'Data Access Request Form',
        ],
        'dcr' => [
            'model' => DataCorrectionRequestForm::class,
            'field_model' => DcrFormField::class,
            'form_id_field' => 'dcr_form_id',
            'title' => 'Data Correction Request Form',
        ],
        'srf' => [
            'model' => ServiceRequestForm::class,
            'field_model' => SrfFormField::class,
            'form_id_field' => 'srf_form_id',
            'title' => 'Service Request Form',
        ],
    ];

    /**
     * Display form builder for a specific form
     */
    public function index($type, $formId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $formModel = $config['model'];
        $fieldModel = $config['field_model'];

        $form = $formModel::findOrFail($formId);
        $fields = $fieldModel::where($config['form_id_field'], $formId)
            ->ordered()
            ->get()
            ->groupBy('field_section');

        $fieldTypes = $fieldModel::getFieldTypes();
        
        // Get sections from database, fallback to defaults if none exist
        $sections = \App\Models\FormSection::getSectionsForFormType($type);
        if (empty($sections)) {
            \App\Models\FormSection::initializeDefaults($type);
            $sections = \App\Models\FormSection::getSectionsForFormType($type);
        }

        // Sort fields by section sort_order from database
        $dbSections = \App\Models\FormSection::forFormType($type)
            ->active()
            ->ordered()
            ->get()
            ->keyBy('section_key');
        
        // Build an array with sort_order as key, then sort by key
        $fieldsWithOrder = [];
        foreach ($fields as $sectionName => $sectionFields) {
            $dbSection = $dbSections->get($sectionName);
            $sortOrder = $dbSection ? $dbSection->sort_order : 999;
            $fieldsWithOrder[$sortOrder] = [
                'name' => $sectionName,
                'fields' => $sectionFields,
            ];
        }
        ksort($fieldsWithOrder);
        
        // Rebuild the collection with sorted sections
        $sortedFields = collect();
        foreach ($fieldsWithOrder as $sectionData) {
            $sortedFields[$sectionData['name']] = $sectionData['fields'];
        }
        $fields = $sortedFields;

        return view('admin.form-builder.index', compact('form', 'type', 'config', 'fields', 'fieldTypes', 'sections'));
    }

    /**
     * Get a single field (for editing)
     */
    public function getField($type, $formId, $fieldId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $fieldModel = $config['field_model'];

        $field = $fieldModel::findOrFail($fieldId);

        // Convert field_options from array to text format for editing
        $fieldOptionsText = '';
        if ($field->field_options && is_array($field->field_options)) {
            $optionsArray = [];
            foreach ($field->field_options as $value => $label) {
                $optionsArray[] = $value . '|' . $label;
            }
            $fieldOptionsText = implode("\n", $optionsArray);
        }

        return response()->json([
            'success' => true,
            'field' => $field,
            'field_options_text' => $fieldOptionsText,
        ]);
    }

    /**
     * Store a new field
     */
    public function storeField(Request $request, $type, $formId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $fieldModel = $config['field_model'];

        $validated = $request->validate([
            'field_section' => 'required|string',
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string',
            'field_placeholder' => 'nullable|string|max:255',
            'field_description' => 'nullable|string',
            'field_help_text' => 'nullable|string',
            'is_required' => 'boolean',
            'is_conditional' => 'boolean',
            'conditional_logic' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_options' => 'nullable|array',
            'field_settings' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'grid_column' => 'nullable|in:left,right,full',
            'is_active' => 'boolean',
        ]);

        // Get max sort_order for this section
        $maxOrder = $fieldModel::where($config['form_id_field'], $formId)
            ->where('field_section', $validated['field_section'])
            ->max('sort_order') ?? 0;

        $validated[$config['form_id_field']] = $formId;
        $validated['sort_order'] = $validated['sort_order'] ?? ($maxOrder + 1);
        $validated['grid_column'] = $validated['grid_column'] ?? 'left';
        $validated['is_required'] = $request->has('is_required');
        $validated['is_conditional'] = $request->has('is_conditional');
        $validated['is_active'] = $request->has('is_active', true);

        $field = $fieldModel::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Added field '{$field->field_label}' to {$config['title']}",
            modelType: $fieldModel,
            modelId: $field->id,
            newValues: $field->toArray()
        );

        return redirect()->route('admin.form-builder.index', [$type, $formId])
            ->with('success', 'Field added successfully!');
    }

    /**
     * Update a field
     */
    public function updateField(Request $request, $type, $formId, $fieldId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $fieldModel = $config['field_model'];

        $field = $fieldModel::findOrFail($fieldId);

        $validated = $request->validate([
            'field_section' => 'required|string',
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string',
            'field_placeholder' => 'nullable|string|max:255',
            'field_description' => 'nullable|string',
            'field_help_text' => 'nullable|string',
            'is_required' => 'boolean',
            'is_conditional' => 'boolean',
            'conditional_logic' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_options' => 'nullable|array',
            'field_settings' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'grid_column' => 'nullable|in:left,right,full',
            'is_active' => 'boolean',
        ]);

        $oldValues = $field->toArray();
        $validated['is_required'] = $request->has('is_required') && $request->input('is_required') == '1';
        $validated['is_conditional'] = $request->has('is_conditional') && $request->input('is_conditional') == '1';
        $validated['is_active'] = $request->has('is_active') && $request->input('is_active') == '1';
        $validated['grid_column'] = $validated['grid_column'] ?? $field->grid_column ?? 'left';

        $field->update($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated field '{$field->field_label}' in {$config['title']}",
            modelType: $fieldModel,
            modelId: $field->id,
            oldValues: $oldValues,
            newValues: $field->toArray()
        );

        return redirect()->route('admin.form-builder.index', [$type, $formId])
            ->with('success', 'Field updated successfully!');
    }

    /**
     * Delete a field
     */
    public function destroyField($type, $formId, $fieldId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $fieldModel = $config['field_model'];

        $field = $fieldModel::findOrFail($fieldId);
        $oldValues = $field->toArray();
        $fieldLabel = $field->field_label;
        
        $field->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted field '{$fieldLabel}' from {$config['title']}",
            modelType: $fieldModel,
            modelId: $fieldId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.form-builder.index', [$type, $formId])
            ->with('success', 'Field deleted successfully!');
    }

    /**
     * Reorder fields
     */
    public function reorderFields(Request $request, $type, $formId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $fieldModel = $config['field_model'];

        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:' . (new $fieldModel)->getTable() . ',id',
            'fields.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->fields as $fieldData) {
            $field = $fieldModel::find($fieldData['id']);
            if ($field) {
                $oldValues = $field->toArray();
                $field->update(['sort_order' => $fieldData['sort_order']]);
                
                $this->logAuditTrail(
                    action: 'update',
                    description: "Reordered field '{$field->field_label}' in {$config['title']}",
                    modelType: $fieldModel,
                    modelId: $field->id,
                    oldValues: $oldValues,
                    newValues: $field->toArray()
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Fields reordered successfully']);
    }

    /**
     * Update field column position
     */
    public function updateFieldColumn(Request $request, $type, $formId, $fieldId)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        $config = $this->formModelMap[$type];
        $fieldModel = $config['field_model'];

        $request->validate([
            'grid_column' => 'required|in:left,right,full',
        ]);

        $field = $fieldModel::findOrFail($fieldId);
        $oldValues = $field->toArray();
        $field->update(['grid_column' => $request->grid_column]);

        $this->logAuditTrail(
            action: 'update',
            description: "Updated column position of field '{$field->field_label}' to '{$request->grid_column}' in {$config['title']}",
            modelType: $fieldModel,
            modelId: $field->id,
            oldValues: $oldValues,
            newValues: $field->toArray()
        );

        return response()->json(['success' => true, 'message' => 'Field column updated successfully']);
    }
}

