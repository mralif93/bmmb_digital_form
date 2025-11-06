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
    public function index(): View
    {
        $forms = Form::latest()->paginate(15);
        
        return view('admin.forms.index', compact('forms'));
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
        $form->load(['sections' => function($query) {
            $query->ordered();
        }, 'fields' => function($query) {
            $query->ordered();
        }]);
        
        // Return JSON if requested via AJAX
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'form' => $form,
                'sections' => $form->sections->map(function($section) {
                    return [
                        'id' => $section->id,
                        'section_key' => $section->section_key,
                        'section_label' => $section->section_label,
                        'section_description' => $section->section_description,
                        'sort_order' => $section->sort_order,
                        'is_active' => $section->is_active,
                        'fields_count' => $section->fields->count(),
                        'fields' => $section->fields->map(function($field) {
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
}
