<?php

namespace App\Http\Controllers\Dcr;

use App\Http\Controllers\Controller;
use App\Models\DataCorrectionRequestForm;
use App\Models\DcrFormField;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DcrFormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataCorrectionRequestForm $form): View|JsonResponse
    {
        $query = $form->formFields()->orderBy('sort_order');

        if ($request->filled('section')) {
            $query->where('field_section', $request->section);
        }

        if ($request->filled('type')) {
            $query->where('field_type', $request->type);
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $fields = $query->get();

        if ($request->expectsJson()) {
            return response()->json([
                'fields' => $fields,
                'form' => $form
            ]);
        }

        return view('admin.dcr.fields.index', compact('fields', 'form'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataCorrectionRequestForm $form): View
    {
        $fieldTypes = DcrFormField::getFieldTypes();
        $fieldSections = DcrFormField::getFieldSections();

        return view('admin.dcr.fields.create', compact('form', 'fieldTypes', 'fieldSections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DataCorrectionRequestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'field_section' => 'required|string|max:100',
            'field_name' => 'required|string|max:100|unique:dcr_form_fields,field_name,NULL,id,dcr_form_id,' . $form->id,
            'field_label' => 'required|string|max:255',
            'field_description' => 'nullable|string',
            'field_type' => 'required|string|in:text,email,phone,number,textarea,select,radio,checkbox,date,file,signature,multiselect',
            'field_placeholder' => 'nullable|string|max:255',
            'field_help_text' => 'nullable|string',
            'is_required' => 'boolean',
            'is_conditional' => 'boolean',
            'conditional_logic' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_options' => 'nullable|array',
            'field_settings' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'css_class' => 'nullable|string|max:255',
            'custom_attributes' => 'nullable|array',
        ]);

        $validated['dcr_form_id'] = $form->id;
        $validated['sort_order'] = $validated['sort_order'] ?? $form->formFields()->max('sort_order') + 1;

        $field = DcrFormField::create($validated);

        return redirect()->route('admin.dcr.forms.fields.index', $form)
            ->with('success', 'Form field created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataCorrectionRequestForm $form, DcrFormField $field): View
    {
        return view('admin.dcr.fields.show', compact('form', 'field'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataCorrectionRequestForm $form, DcrFormField $field): View
    {
        $fieldTypes = DcrFormField::getFieldTypes();
        $fieldSections = DcrFormField::getFieldSections();

        return view('admin.dcr.fields.edit', compact('form', 'field', 'fieldTypes', 'fieldSections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataCorrectionRequestForm $form, DcrFormField $field): RedirectResponse
    {
        $validated = $request->validate([
            'field_section' => 'required|string|max:100',
            'field_name' => 'required|string|max:100|unique:dcr_form_fields,field_name,' . $field->id . ',id,dcr_form_id,' . $form->id,
            'field_label' => 'required|string|max:255',
            'field_description' => 'nullable|string',
            'field_type' => 'required|string|in:text,email,phone,number,textarea,select,radio,checkbox,date,file,signature,multiselect',
            'field_placeholder' => 'nullable|string|max:255',
            'field_help_text' => 'nullable|string',
            'is_required' => 'boolean',
            'is_conditional' => 'boolean',
            'conditional_logic' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_options' => 'nullable|array',
            'field_settings' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'css_class' => 'nullable|string|max:255',
            'custom_attributes' => 'nullable|array',
        ]);

        $field->update($validated);

        return redirect()->route('admin.dcr.forms.fields.index', $form)
            ->with('success', 'Form field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataCorrectionRequestForm $form, DcrFormField $field): RedirectResponse
    {
        $field->delete();

        return redirect()->route('admin.dcr.forms.fields.index', $form)
            ->with('success', 'Form field deleted successfully.');
    }

    /**
     * Toggle field active status.
     */
    public function toggleStatus(DataCorrectionRequestForm $form, DcrFormField $field): JsonResponse
    {
        $field->update(['is_active' => !$field->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Field status updated successfully.',
            'field' => $field->fresh()
        ]);
    }

    /**
     * Update field sort order.
     */
    public function updateOrder(Request $request, DataCorrectionRequestForm $form): JsonResponse
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:dcr_form_fields,id',
            'fields.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['fields'] as $fieldData) {
            DcrFormField::where('id', $fieldData['id'])
                ->where('dcr_form_id', $form->id)
                ->update(['sort_order' => $fieldData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Field order updated successfully.'
        ]);
    }

    /**
     * Duplicate a field.
     */
    public function duplicate(DataCorrectionRequestForm $form, DcrFormField $field): RedirectResponse
    {
        $newField = $field->replicate();
        $newField->field_name = $field->field_name . '_copy_' . time();
        $newField->field_label = $field->field_label . ' (Copy)';
        $newField->sort_order = $form->formFields()->max('sort_order') + 1;
        $newField->save();

        return redirect()->route('admin.dcr.forms.fields.index', $form)
            ->with('success', 'Form field duplicated successfully.');
    }

    /**
     * Get field options for select fields.
     */
    public function getFieldOptions(DataCorrectionRequestForm $form, DcrFormField $field): JsonResponse
    {
        if (!$field->hasOptions()) {
            return response()->json(['options' => []]);
        }

        return response()->json([
            'options' => $field->getOptions()
        ]);
    }

    /**
     * Update field options for select fields.
     */
    public function updateFieldOptions(Request $request, DataCorrectionRequestForm $form, DcrFormField $field): JsonResponse
    {
        $validated = $request->validate([
            'options' => 'required|array',
            'options.*.value' => 'required|string',
            'options.*.label' => 'required|string',
        ]);

        $field->update(['field_options' => $validated['options']]);

        return response()->json([
            'success' => true,
            'message' => 'Field options updated successfully.',
            'field' => $field->fresh()
        ]);
    }
}