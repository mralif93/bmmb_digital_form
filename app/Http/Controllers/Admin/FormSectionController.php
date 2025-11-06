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
    public function show(Form $form, FormSection $section): View
    {
        // Ensure section belongs to this form
        if ($section->form_id !== $form->id) {
            abort(404);
        }

        return view('admin.form-sections.show', compact('form', 'section'));
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
    public function reorder(Request $request, Form $form): RedirectResponse
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

        return redirect()->route('admin.form-sections.index', $form)
            ->with('success', 'Sections reordered successfully!');
    }
}
