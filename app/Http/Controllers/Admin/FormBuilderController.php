<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormField;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display form builder for a specific form
     */
    public function index(Form $form)
    {
        // Ensure form has sections - initialize defaults if needed
        if ($form->sections()->count() === 0) {
            $formType = $form->settings['type'] ?? $form->slug ?? 'raf';
            FormSection::initializeDefaults($form->id, $formType);
        }

        $form->load(['sections' => function($query) {
            $query->ordered();
        }, 'fields' => function($query) {
            $query->ordered();
        }]);

        // Group fields by section
        $fieldsBySection = $form->fields->groupBy('section_id');
        
        // Get sections with their fields
        $sectionsWithFields = [];
        foreach ($form->sections as $section) {
            $sectionsWithFields[$section->id] = [
                'section' => $section,
                'fields' => $fieldsBySection->get($section->id, collect())->sortBy('sort_order'),
            ];
        }

        $fieldTypes = FormField::getFieldTypes();

        return view('admin.form-builder.index', compact('form', 'sectionsWithFields', 'fieldTypes'));
    }

    /**
     * Get a single field (for editing)
     */
    public function getField(Form $form, FormField $field)
    {
        // Ensure field belongs to this form
        if ($field->form_id !== $form->id) {
            abort(404);
        }

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
     * Display the specified field (for viewing)
     */
    public function show(Form $form, FormField $field)
    {
        // Ensure field belongs to this form
        if ($field->form_id !== $form->id) {
            abort(404);
        }

        // Load relationships
        $field->load('section');

        // Return JSON if requested via AJAX
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'field' => [
                    'id' => $field->id,
                    'field_name' => $field->field_name,
                    'field_label' => $field->field_label,
                    'field_type' => $field->field_type,
                    'field_options' => $field->field_options,
                    'field_placeholder' => $field->field_placeholder,
                    'field_help_text' => $field->field_help_text,
                    'field_default_value' => $field->field_default_value,
                    'field_validation_rules' => $field->field_validation_rules,
                    'grid_column' => $field->grid_column,
                    'sort_order' => $field->sort_order,
                    'is_active' => $field->is_active,
                    'is_required' => $field->is_required,
                    'is_conditional' => $field->is_conditional,
                    'conditional_field' => $field->conditional_field,
                    'conditional_value' => $field->conditional_value,
                    'created_at' => $field->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $field->updated_at?->format('Y-m-d H:i:s'),
                    'section' => $field->section ? [
                        'id' => $field->section->id,
                        'section_key' => $field->section->section_key,
                        'section_label' => $field->section->section_label,
                    ] : null,
                ],
            ]);
        }

        return view('admin.form-builder.show', compact('form', 'field'));
    }

    /**
     * Store a new field
     */
    public function storeField(Request $request, Form $form)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:form_sections,id',
            'field_name' => 'required|string|max:255|unique:form_fields,field_name,NULL,id,form_id,' . $form->id,
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

        // Verify section belongs to this form
        $section = FormSection::where('id', $validated['section_id'])
            ->where('form_id', $form->id)
            ->firstOrFail();

        // Get max sort_order for this section
        $maxOrder = FormField::where('form_id', $form->id)
            ->where('section_id', $validated['section_id'])
            ->max('sort_order') ?? 0;

        $validated['form_id'] = $form->id;
        $validated['sort_order'] = $validated['sort_order'] ?? ($maxOrder + 1);
        $validated['grid_column'] = $validated['grid_column'] ?? 'left';
        $validated['is_required'] = $request->has('is_required');
        $validated['is_conditional'] = $request->has('is_conditional');
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $field = FormField::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Added field '{$field->field_label}' to form '{$form->name}'",
            modelType: FormField::class,
            modelId: $field->id,
            newValues: $field->toArray()
        );

        return redirect()->route('admin.form-builder.index', $form)
            ->with('success', 'Field added successfully!');
    }

    /**
     * Update a field
     */
    public function updateField(Request $request, Form $form, FormField $field)
    {
        // Ensure field belongs to this form
        if ($field->form_id !== $form->id) {
            abort(404);
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:form_sections,id',
            'field_name' => 'required|string|max:255|unique:form_fields,field_name,' . $field->id . ',id,form_id,' . $form->id,
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

        // Verify section belongs to this form
        $section = FormSection::where('id', $validated['section_id'])
            ->where('form_id', $form->id)
            ->firstOrFail();

        $oldValues = $field->toArray();
        $validated['is_required'] = $request->has('is_required') && $request->input('is_required') == '1';
        $validated['is_conditional'] = $request->has('is_conditional') && $request->input('is_conditional') == '1';
        $validated['is_active'] = $request->has('is_active') && $request->input('is_active') == '1';
        $validated['grid_column'] = $validated['grid_column'] ?? $field->grid_column ?? 'left';

        $field->update($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated field '{$field->field_label}' in form '{$form->name}'",
            modelType: FormField::class,
            modelId: $field->id,
            oldValues: $oldValues,
            newValues: $field->toArray()
        );

        return redirect()->route('admin.form-builder.index', $form)
            ->with('success', 'Field updated successfully!');
    }

    /**
     * Delete a field
     */
    public function destroyField(Form $form, FormField $field)
    {
        // Ensure field belongs to this form
        if ($field->form_id !== $form->id) {
            abort(404);
        }

        $oldValues = $field->toArray();
        $fieldLabel = $field->field_label;
        
        $field->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted field '{$fieldLabel}' from form '{$form->name}'",
            modelType: FormField::class,
            modelId: $field->id,
            oldValues: $oldValues
        );

        return redirect()->route('admin.form-builder.index', $form)
            ->with('success', 'Field deleted successfully!');
    }

    /**
     * Reorder fields
     */
    public function reorderFields(Request $request, Form $form)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:form_fields,id',
            'fields.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->fields as $fieldData) {
            $field = FormField::where('id', $fieldData['id'])
                ->where('form_id', $form->id)
                ->first();
            
            if ($field) {
                $oldValues = $field->toArray();
                $field->update(['sort_order' => $fieldData['sort_order']]);
                
                $this->logAuditTrail(
                    action: 'update',
                    description: "Reordered field '{$field->field_label}' in form '{$form->name}'",
                    modelType: FormField::class,
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
    public function updateFieldColumn(Request $request, Form $form, FormField $field)
    {
        // Ensure field belongs to this form
        if ($field->form_id !== $form->id) {
            abort(404);
        }

        $request->validate([
            'grid_column' => 'required|in:left,right,full',
        ]);

        $oldValues = $field->toArray();
        $field->update(['grid_column' => $request->grid_column]);

        $this->logAuditTrail(
            action: 'update',
            description: "Updated column position of field '{$field->field_label}' to '{$request->grid_column}' in form '{$form->name}'",
            modelType: FormField::class,
            modelId: $field->id,
            oldValues: $oldValues,
            newValues: $field->toArray()
        );

        return response()->json(['success' => true, 'message' => 'Field column updated successfully']);
    }
}
