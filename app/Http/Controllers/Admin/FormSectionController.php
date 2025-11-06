<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSection;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FormSectionController extends Controller
{
    use LogsAuditTrail;
    /**
     * Display a listing of sections for a specific form type.
     */
    public function index(string $type): View
    {
        $sections = FormSection::forFormType($type)
            ->ordered()
            ->get();

        // Initialize defaults if no sections exist
        if ($sections->isEmpty()) {
            FormSection::initializeDefaults($type);
            $sections = FormSection::forFormType($type)->ordered()->get();
        }

        $formTypeLabels = [
            'raf' => 'Remittance Application Form',
            'dar' => 'Data Access Request Form',
            'dcr' => 'Data Correction Request Form',
            'srf' => 'Service Request Form',
        ];

        return view('admin.form-sections.index', compact('sections', 'type', 'formTypeLabels'));
    }

    /**
     * Show the form for creating a new section.
     */
    public function create(string $type): View
    {
        // Get max sort_order for this form type
        $maxOrder = FormSection::forFormType($type)->max('sort_order') ?? 0;

        return view('admin.form-sections.create', compact('type', 'maxOrder'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request, string $type): RedirectResponse
    {
        $validated = $request->validate([
            'section_key' => 'required|string|max:100|unique:form_sections,section_key,NULL,id,form_type,' . $type,
            'section_label' => 'required|string|max:255',
            'section_description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['form_type'] = $type;
        $validated['is_active'] = $request->has('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? (FormSection::forFormType($type)->max('sort_order') ?? 0) + 1;

        $section = FormSection::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created section '{$section->section_label}' for " . strtoupper($type) . " form",
            modelType: FormSection::class,
            modelId: $section->id,
            newValues: $section->toArray()
        );

        return redirect()->route('admin.form-sections.index', $type)
            ->with('success', 'Section created successfully!');
    }

    /**
     * Display the specified section.
     */
    public function show(string $type, FormSection $section): View
    {
        return view('admin.form-sections.show', compact('section', 'type'));
    }

    /**
     * Show the form for editing the specified section.
     */
    public function edit(string $type, FormSection $section): View
    {
        return view('admin.form-sections.edit', compact('section', 'type'));
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, string $type, FormSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'section_key' => 'required|string|max:100|unique:form_sections,section_key,' . $section->id . ',id,form_type,' . $type,
            'section_label' => 'required|string|max:255',
            'section_description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active', true);

        $oldValues = $section->toArray();
        $section->update($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated section '{$section->section_label}' for " . strtoupper($type) . " form",
            modelType: FormSection::class,
            modelId: $section->id,
            oldValues: $oldValues,
            newValues: $section->toArray()
        );

        return redirect()->route('admin.form-sections.index', $type)
            ->with('success', 'Section updated successfully!');
    }

    /**
     * Remove the specified section.
     */
    public function destroy(string $type, FormSection $section): RedirectResponse
    {
        // Check if section has fields
        $hasFields = false;
        $fieldModelMap = [
            'raf' => \App\Models\RafFormField::class,
            'dar' => \App\Models\DarFormField::class,
            'dcr' => \App\Models\DcrFormField::class,
            'srf' => \App\Models\SrfFormField::class,
        ];

        if (isset($fieldModelMap[$type])) {
            $fieldModel = $fieldModelMap[$type];
            $formIdField = $type . '_form_id';
            $hasFields = $fieldModel::where('field_section', $section->section_key)->exists();
        }

        if ($hasFields) {
            return redirect()->route('admin.form-sections.index', $type)
                ->with('error', 'Cannot delete section. It contains form fields. Please remove all fields first or move them to another section.');
        }

        $oldValues = $section->toArray();
        $sectionLabel = $section->section_label;
        $section->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted section '{$sectionLabel}' from " . strtoupper($type) . " form",
            modelType: FormSection::class,
            modelId: $section->id,
            oldValues: $oldValues
        );

        return redirect()->route('admin.form-sections.index', $type)
            ->with('success', 'Section deleted successfully!');
    }

    /**
     * Reorder sections
     */
    public function reorder(Request $request, string $type): RedirectResponse
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:form_sections,id',
            'sections.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->sections as $item) {
            $section = FormSection::where('id', $item['id'])
                ->where('form_type', $type)
                ->first();
            
            if ($section) {
                $oldValues = $section->toArray();
                $section->update(['sort_order' => $item['sort_order']]);
                
                // Log audit trail
                $this->logAuditTrail(
                    action: 'update',
                    description: "Reordered section '{$section->section_label}' in " . strtoupper($type) . " form",
                    modelType: FormSection::class,
                    modelId: $section->id,
                    oldValues: $oldValues,
                    newValues: $section->toArray()
                );
            }
        }

        return redirect()->route('admin.form-sections.index', $type)
            ->with('success', 'Sections reordered successfully!');
    }
}
