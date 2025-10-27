<?php

namespace App\Http\Controllers\Dar;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DarFormField;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DarFormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataAccessRequestForm $form): View|JsonResponse
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

        return view('admin.dar.fields.index', compact('fields', 'form'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataAccessRequestForm $form): View
    {
        $fieldTypes = DarFormField::getFieldTypes();
        $fieldSections = DarFormField::getFieldSections();

        return view('admin.dar.fields.create', compact('form', 'fieldTypes', 'fieldSections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DataAccessRequestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'field_section' => 'required|string|max:100',
            'field_name' => 'required|string|max:100|unique:dar_form_fields,field_name,NULL,id,dar_form_id,' . $form->id,
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

        $validated['dar_form_id'] = $form->id;
        $validated['sort_order'] = $validated['sort_order'] ?? $form->formFields()->max('sort_order') + 1;

        $field = DarFormField::create($validated);

        return redirect()->route('admin.dar.forms.fields.index', $form)
            ->with('success', 'Form field created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataAccessRequestForm $form, DarFormField $field): View
    {
        return view('admin.dar.fields.show', compact('form', 'field'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataAccessRequestForm $form, DarFormField $field): View
    {
        $fieldTypes = DarFormField::getFieldTypes();
        $fieldSections = DarFormField::getFieldSections();

        return view('admin.dar.fields.edit', compact('form', 'field', 'fieldTypes', 'fieldSections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataAccessRequestForm $form, DarFormField $field): RedirectResponse
    {
        $validated = $request->validate([
            'field_section' => 'required|string|max:100',
            'field_name' => 'required|string|max:100|unique:dar_form_fields,field_name,' . $field->id . ',id,dar_form_id,' . $form->id,
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

        return redirect()->route('admin.dar.forms.fields.index', $form)
            ->with('success', 'Form field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataAccessRequestForm $form, DarFormField $field): RedirectResponse
    {
        $field->delete();

        return redirect()->route('admin.dar.forms.fields.index', $form)
            ->with('success', 'Form field deleted successfully.');
    }

    /**
     * Toggle field active status.
     */
    public function toggleStatus(DataAccessRequestForm $form, DarFormField $field): JsonResponse
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
    public function updateOrder(Request $request, DataAccessRequestForm $form): JsonResponse
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:dar_form_fields,id',
            'fields.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['fields'] as $fieldData) {
            DarFormField::where('id', $fieldData['id'])
                ->where('dar_form_id', $form->id)
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
    public function duplicate(DataAccessRequestForm $form, DarFormField $field): RedirectResponse
    {
        $newField = $field->replicate();
        $newField->field_name = $field->field_name . '_copy_' . time();
        $newField->field_label = $field->field_label . ' (Copy)';
        $newField->sort_order = $form->formFields()->max('sort_order') + 1;
        $newField->save();

        return redirect()->route('admin.dar.forms.fields.index', $form)
            ->with('success', 'Form field duplicated successfully.');
    }

    /**
     * Get field options for select fields.
     */
    public function getFieldOptions(DataAccessRequestForm $form, DarFormField $field): JsonResponse
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
    public function updateFieldOptions(Request $request, DataAccessRequestForm $form, DarFormField $field): JsonResponse
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