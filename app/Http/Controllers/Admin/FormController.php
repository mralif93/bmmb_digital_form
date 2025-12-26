<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class FormController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $showTrashed = $request->query('trashed') === 'true';

        if ($showTrashed) {
            $forms = Form::onlyTrashed()->orderBy('sort_order')->orderBy('name')->paginate(15);
        } else {
            $forms = Form::orderBy('sort_order')->orderBy('name')->paginate(15);
        }

        return view('admin.forms.index', compact('forms', 'showTrashed'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $statusOptions = Form::getStatusOptions();

        return view('admin.forms.create', compact('statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,inactive',
            'is_public' => 'boolean',
            'allow_multiple_submissions' => 'boolean',
            'submission_limit' => 'nullable|integer|min:1',
            'settings' => 'nullable|array',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Form::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $validated['is_public'] = $request->has('is_public');
        $validated['allow_multiple_submissions'] = $request->has('allow_multiple_submissions');

        $form = Form::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created form '{$form->name}'",
            modelType: Form::class,
            modelId: $form->id,
            newValues: $form->toArray()
        );

        return redirect()->route('admin.forms.index')
            ->with('success', 'Form created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        $form->load([
            'sections' => function ($query) {
                $query->ordered();
            },
            'fields' => function ($query) {
                $query->ordered();
            }
        ]);

        // Return JSON if requested via AJAX
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'form' => $form,
                'sections' => $form->sections->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'section_key' => $section->section_key,
                        'section_label' => $section->section_label,
                        'section_description' => $section->section_description,
                        'sort_order' => $section->sort_order,
                        'is_active' => $section->is_active,
                        'fields_count' => $section->fields->count(),
                        'fields' => $section->fields->map(function ($field) {
                            return [
                                'id' => $field->id,
                                'field_name' => $field->field_name,
                                'field_label' => $field->field_label,
                                'field_type' => $field->field_type,
                            ];
                        }),
                    ];
                }),
            ]);
        }

        return view('admin.forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form): View
    {
        $statusOptions = Form::getStatusOptions();

        return view('admin.forms.edit', compact('form', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug,' . $form->id,
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,inactive',
            'is_public' => 'boolean',
            'allow_multiple_submissions' => 'boolean',
            'submission_limit' => 'nullable|integer|min:1',
            'settings' => 'nullable|array',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Form::where('slug', $validated['slug'])->where('id', '!=', $form->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $oldValues = $form->toArray();
        $validated['is_public'] = $request->has('is_public');
        $validated['allow_multiple_submissions'] = $request->has('allow_multiple_submissions');


        $form->update($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated form '{$form->name}'",
            modelType: Form::class,
            modelId: $form->id,
            oldValues: $oldValues,
            newValues: $form->toArray()
        );

        return redirect()->route('admin.forms.index')
            ->with('success', 'Form updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form): RedirectResponse
    {
        $oldValues = $form->toArray();
        $formName = $form->name;

        $form->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted form '{$formName}'",
            modelType: Form::class,
            modelId: $oldValues['id'],
            oldValues: $oldValues
        );

        return redirect()->route('admin.forms.index')
            ->with('success', 'Form deleted successfully!');
    }

    /**
     * Reorder forms
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'forms' => 'required|array',
            'forms.*.id' => 'required|exists:forms,id',
            'forms.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->forms as $formData) {
            Form::where('id', $formData['id'])
                ->update(['sort_order' => $formData['sort_order']]);
        }

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: 'Reordered forms',
            modelType: Form::class,
            modelId: null
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Forms reordered successfully',
            ]);
        }

        return redirect()->route('admin.forms.index')
            ->with('success', 'Forms reordered successfully!');
    }

    /**
     * Export form structure to JSON
     */
    public function export(Form $form)
    {
        // Load form with all sections and their fields
        $form->load([
            'sections' => function ($query) {
                $query->ordered()->with([
                    'fields' => function ($q) {
                        $q->ordered();
                    }
                ]);
            }
        ]);

        // Build JSON structure matching seeder format
        $export = [
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
                'slug' => $form->slug,
                'description' => $form->description,
                'status' => $form->status,
                'is_public' => $form->is_public,
                'allow_multiple_submissions' => $form->allow_multiple_submissions,
                'submission_limit' => $form->submission_limit,
                'sort_order' => $form->sort_order,
                'settings' => $form->settings ?? new \stdClass(),
            ],
            'sections' => []
        ];

        // Add sections and fields
        foreach ($form->sections as $section) {
            $sectionData = [
                'id' => $section->id,
                'key' => $section->section_key,
                'title' => $section->section_label,
                'description' => $section->section_description,
                'sort_order' => $section->sort_order,
                'is_active' => $section->is_active,
                'grid_layout' => $section->grid_layout ?? '2-column',
                'fields' => []
            ];

            foreach ($section->fields as $field) {
                $sectionData['fields'][] = [
                    'name' => $field->field_name,
                    'label' => $field->field_label,
                    'type' => $field->field_type,
                    'placeholder' => $field->field_placeholder,
                    'description' => $field->field_description,
                    'help_text' => $field->help_text,
                    'required' => $field->is_required,
                    'active' => $field->is_active,
                    'sort_order' => $field->sort_order,
                    'grid_column' => $field->grid_column ?? 'left',
                    'conditional' => !empty($field->conditional_logic),
                    'conditional_logic' => $field->conditional_logic,
                    'options' => $field->field_options,
                    'settings' => $field->field_settings ?? new \stdClass(),
                    'validation' => $field->validation_rules,
                ];
            }

            $export['sections'][] = $sectionData;
        }

        // Log audit trail
        $this->logAuditTrail(
            action: 'export',
            description: "Exported form '{$form->name}' to JSON",
            modelType: Form::class,
            modelId: $form->id
        );

        // Return as downloadable JSON file
        $filename = $form->slug . '_export.json';
        return response()->json($export, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Import form structure from JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json|max:5120', // Max 5MB
        ]);

        try {
            // Read and parse JSON file
            $jsonContent = file_get_contents($request->file('json_file')->getRealPath());
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()
                    ->with('error', 'Invalid JSON file: ' . json_last_error_msg());
            }

            // Validate JSON structure
            if (!isset($data['form']) || !isset($data['sections'])) {
                return redirect()->back()
                    ->with('error', 'Invalid JSON structure: missing form or sections data');
            }

            if (!isset($data['form']['name']) || !isset($data['form']['slug'])) {
                return redirect()->back()
                    ->with('error', 'Invalid JSON structure: form must have name and slug');
            }

            // Begin database transaction
            \DB::beginTransaction();

            try {
                // Check if form exists by slug (update existing)
                $form = Form::where('slug', $data['form']['slug'])->first();

                if ($form) {
                    // Update existing form
                    $form->update([
                        'name' => $data['form']['name'],
                        'description' => $data['form']['description'] ?? null,
                        'status' => $data['form']['status'] ?? 'draft',
                        'is_public' => $data['form']['is_public'] ?? false,
                        'allow_multiple_submissions' => $data['form']['allow_multiple_submissions'] ?? false,
                        'submission_limit' => $data['form']['submission_limit'] ?? null,
                        'settings' => $data['form']['settings'] ?? null,
                    ]);

                    // Delete existing sections and fields (will cascade)
                    $form->sections()->delete();

                    $action = 'updated';
                } else {
                    // Create new form
                    $form = Form::create([
                        'name' => $data['form']['name'],
                        'slug' => $data['form']['slug'],
                        'description' => $data['form']['description'] ?? null,
                        'status' => $data['form']['status'] ?? 'draft',
                        'is_public' => $data['form']['is_public'] ?? false,
                        'allow_multiple_submissions' => $data['form']['allow_multiple_submissions'] ?? false,
                        'submission_limit' => $data['form']['submission_limit'] ?? null,
                        'sort_order' => $data['form']['sort_order'] ?? 0,
                        'settings' => $data['form']['settings'] ?? null,
                    ]);

                    $action = 'created';
                }

                // Import sections and fields
                foreach ($data['sections'] as $sectionData) {
                    $section = $form->sections()->create([
                        'section_key' => $sectionData['key'],
                        'section_label' => $sectionData['title'] ?? $sectionData['key'],
                        'section_description' => $sectionData['description'] ?? null,
                        'sort_order' => $sectionData['sort_order'] ?? 0,
                        'is_active' => $sectionData['is_active'] ?? true,
                        'grid_layout' => $sectionData['grid_layout'] ?? '2-column',
                    ]);

                    // Import fields for this section
                    if (isset($sectionData['fields']) && is_array($sectionData['fields'])) {
                        foreach ($sectionData['fields'] as $fieldData) {
                            $section->fields()->create([
                                'form_id' => $form->id,
                                'field_name' => $fieldData['name'],
                                'field_label' => $fieldData['label'],
                                'field_type' => $fieldData['type'],
                                'field_placeholder' => $fieldData['placeholder'] ?? null,
                                'field_description' => $fieldData['description'] ?? null,
                                'help_text' => $fieldData['help_text'] ?? null,
                                'is_required' => $fieldData['required'] ?? false,
                                'is_active' => $fieldData['active'] ?? true,
                                'sort_order' => $fieldData['sort_order'] ?? 0,
                                'grid_column' => $fieldData['grid_column'] ?? 'left',
                                'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                                'field_options' => $fieldData['options'] ?? null,
                                'field_settings' => $fieldData['settings'] ?? null,
                                'validation_rules' => $fieldData['validation'] ?? null,
                            ]);
                        }
                    }
                }

                // Commit transaction
                \DB::commit();

                // Log audit trail
                $this->logAuditTrail(
                    action: 'import',
                    description: "Imported form '{$form->name}' from JSON ({$action})",
                    modelType: Form::class,
                    modelId: $form->id
                );

                return redirect()->route('admin.forms.index')
                    ->with('success', "Form '{$form->name}' {$action} successfully from JSON!");

            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to import form: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted form
     */
    public function restore($id): RedirectResponse
    {
        try {
            $form = Form::onlyTrashed()->findOrFail($id);
            $form->restore();

            // Log audit trail
            $this->logAuditTrail(
                action: 'restore',
                description: "Restored form '{$form->name}'",
                modelType: Form::class,
                modelId: $form->id
            );

            return redirect()->route('admin.forms.index')
                ->with('success', "Form '{$form->name}' restored successfully!");

        } catch (\Exception $e) {
            \Log::error("Error restoring form ID {$id}: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore form: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a soft-deleted form
     */
    public function forceDelete($id): RedirectResponse
    {
        try {
            $form = Form::onlyTrashed()->findOrFail($id);
            $formName = $form->name;

            // Manually delete related records to prevent Foreign Key constraints
            // (If Cascades are not set in DB)
            $form->submissions()->forceDelete(); // Delete submissions

            // Delete sections and fields (fetch even trashed ones if any)
            foreach ($form->sections()->withTrashed()->get() as $section) {
                $section->fields()->withTrashed()->forceDelete(); // Delete fields first
                $section->forceDelete(); // Then section
            }

            // Finally permanently delete the form
            $form->forceDelete();

            // Log audit trail
            $this->logAuditTrail(
                action: 'force_delete',
                description: "Permanently deleted form '{$formName}'",
                modelType: Form::class,
                modelId: $id
            );

            // Check if there are any more trashed forms
            $remainingTrashedCount = Form::onlyTrashed()->count();

            if ($remainingTrashedCount > 0) {
                // Stay on deleted forms view
                return redirect()->route('admin.forms.index', ['trashed' => 'true'])
                    ->with('success', "Form '{$formName}' permanently deleted!");
            } else {
                // No more deleted forms, go back to active forms
                return redirect()->route('admin.forms.index')
                    ->with('success', "Form '{$formName}' permanently deleted! No more deleted forms.");
            }

        } catch (\Exception $e) {
            \Log::error("Error force deleting form ID {$id}: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete form: ' . $e->getMessage());
        }
    }

}
