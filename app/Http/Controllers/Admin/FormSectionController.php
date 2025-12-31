<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSection;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FormSectionController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of sections for a specific form.
     */
    public function index(Form $form): View
    {
        $sections = $form->sections()->ordered()->get();

        return view('admin.form-sections.index', compact('form', 'sections'));
    }

    /**
     * Show the form for creating a new section.
     */
    public function create(Form $form): View
    {
        // Get max sort_order for this form
        $maxOrder = $form->sections()->max('sort_order') ?? 0;

        return view('admin.form-sections.create', compact('form', 'maxOrder'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request, Form $form): RedirectResponse
    {
        $validated = $request->validate([
            'section_key' => 'required|string|max:100|unique:form_sections,section_key,NULL,id,form_id,' . $form->id,
            'section_label' => 'required|string|max:255',
            'section_description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['form_id'] = $form->id;
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['sort_order'] = $validated['sort_order'] ?? ($form->sections()->max('sort_order') ?? 0) + 1;

        $section = FormSection::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created section '{$section->section_label}' for form '{$form->name}'",
            modelType: FormSection::class,
            modelId: $section->id,
            newValues: $section->toArray()
        );

        return redirect()->route('admin.form-sections.index', $form)
            ->with('success', 'Section created successfully!');
    }

    /**
     * Display the specified section.
     */
    public function show(Form $form, FormSection $section)
    {
        // Ensure section belongs to this form
        if ($section->form_id !== $form->id) {
            abort(404);
        }

        // Load relationships
        $section->load('fields');

        $timezoneHelper = app(\App\Helpers\TimezoneHelper::class);
        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i:s';

        // Return JSON if requested via AJAX
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'section' => [
                    'id' => $section->id,
                    'section_key' => $section->section_key,
                    'section_label' => $section->section_label,
                    'section_description' => $section->section_description,
                    'sort_order' => $section->sort_order,
                    'is_active' => $section->is_active,
                    'created_at' => $timezoneHelper->convert($section->created_at)?->format($dateFormat . ' ' . $timeFormat),
                    'updated_at' => $timezoneHelper->convert($section->updated_at)?->format($dateFormat . ' ' . $timeFormat),
                    'fields_count' => $section->fields->count(),
                    'fields' => $section->fields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'field_name' => $field->field_name,
                            'field_label' => $field->field_label,
                            'field_type' => $field->field_type,
                        ];
                    }),
                ],
            ]);
        }

        return view('admin.form-sections.show', compact('form', 'section', 'timezoneHelper', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show the form for editing the specified section.
     */
    public function edit(Form $form, FormSection $section): View
    {
        // Ensure section belongs to this form
        if ($section->form_id !== $form->id) {
            abort(404);
        }

        return view('admin.form-sections.edit', compact('form', 'section'));
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, Form $form, FormSection $section): RedirectResponse
    {
        // Ensure section belongs to this form
        if ($section->form_id !== $form->id) {
            abort(404);
        }

        $validated = $request->validate([
            'section_key' => 'required|string|max:100|unique:form_sections,section_key,' . $section->id . ',id,form_id,' . $form->id,
            'section_label' => 'required|string|max:255',
            'section_description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $oldValues = $section->toArray();
        $section->update($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated section '{$section->section_label}' for form '{$form->name}'",
            modelType: FormSection::class,
            modelId: $section->id,
            oldValues: $oldValues,
            newValues: $section->toArray()
        );

        return redirect()->route('admin.form-sections.index', $form)
            ->with('success', 'Section updated successfully!');
    }

    /**
     * Remove the specified section.
     */
    public function destroy(Form $form, FormSection $section): RedirectResponse
    {
        // Ensure section belongs to this form
        if ($section->form_id !== $form->id) {
            abort(404);
        }

        // Check if section has fields
        $hasFields = $section->fields()->exists();

        if ($hasFields) {
            return redirect()->route('admin.form-sections.index', $form)
                ->with('error', 'Cannot delete section. It contains form fields. Please remove all fields first or move them to another section.');
        }

        $oldValues = $section->toArray();
        $sectionLabel = $section->section_label;
        $section->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted section '{$sectionLabel}' from form '{$form->name}'",
            modelType: FormSection::class,
            modelId: $section->id,
            oldValues: $oldValues
        );

        return redirect()->route('admin.form-sections.index', $form)
            ->with('success', 'Section deleted successfully!');
    }

    /**
     * Reorder sections
     */
    public function reorder(Request $request, Form $form)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:form_sections,id',
            'sections.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->sections as $item) {
            $section = FormSection::where('id', $item['id'])
                ->where('form_id', $form->id)
                ->first();

            if ($section) {
                $oldValues = $section->toArray();
                $section->update(['sort_order' => $item['sort_order']]);

                // Log audit trail
                $this->logAuditTrail(
                    action: 'update',
                    description: "Reordered section '{$section->section_label}' in form '{$form->name}'",
                    modelType: FormSection::class,
                    modelId: $section->id,
                    oldValues: $oldValues,
                    newValues: $section->toArray()
                );
            }
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sections reordered successfully']);
        }

        return redirect()->route('admin.form-sections.index', $form)
            ->with('success', 'Sections reordered successfully!');
    }

    /**
     * Update section grid layout (2-column vs 6-column)
     */
    public function updateGridLayout(Request $request, Form $form, FormSection $section)
    {
        // Ensure section belongs to this form
        if ($section->form_id !== $form->id) {
            abort(404);
        }

        $validated = $request->validate([
            'grid_layout' => 'required|in:2-column,6-column',
        ]);

        $oldLayout = $section->grid_layout;
        $section->update($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Changed grid layout for section '{$section->section_label}' from '{$oldLayout}' to '{$validated['grid_layout']}'",
            modelType: FormSection::class,
            modelId: $section->id,
            oldValues: ['grid_layout' => $oldLayout],
            newValues: ['grid_layout' => $validated['grid_layout']]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Grid layout updated successfully!',
                'section' => $section,
            ]);
        }

        return redirect()->route('admin.form-sections.index', $form)
            ->with('success', 'Grid layout updated successfully!');
    }
}
