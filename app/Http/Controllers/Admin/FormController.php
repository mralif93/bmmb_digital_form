<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::with('creator')->latest()->paginate(10);
        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'form_type' => 'nullable|string|in:single,multi,wizard',
            'step_count' => 'nullable|integer|min:2|max:5',
            'steps' => 'nullable|array',
            'fields' => 'required|array|min:1',
            'fields.*.field_type' => 'required|string',
            'fields.*.field_name' => 'required|string',
            'fields.*.field_label' => 'required|string',
            'fields.*.step' => 'nullable|integer|min:1',
        ]);

        $settings = [
            'form_type' => $request->form_type ?? 'single',
            'step_count' => $request->step_count ?? 1,
            'steps' => $request->steps ?? [],
        ];

        $form = Form::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'created_by' => auth()->id(),
            'settings' => $settings,
            'form_type' => $request->form_type ?? 'single',
            'step_count' => $request->step_count ?? 1,
            'is_active' => $request->boolean('is_active'),
            'is_public' => $request->boolean('is_public', true),
        ]);

        // Create form fields
        foreach ($request->fields as $index => $fieldData) {
            FormField::create([
                'form_id' => $form->id,
                'field_type' => $fieldData['field_type'],
                'field_name' => $fieldData['field_name'],
                'field_label' => $fieldData['field_label'],
                'field_description' => $fieldData['field_description'] ?? null,
                'field_options' => $fieldData['field_options'] ?? null,
                'validation_rules' => $fieldData['validation_rules'] ?? [],
                'is_required' => $fieldData['is_required'] ?? false,
                'sort_order' => $index,
                'step' => $fieldData['step'] ?? 1,
            ]);
        }

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully!');
    }

    public function show(Form $form)
    {
        $form->load('fields', 'creator');
        return view('admin.forms.show', compact('form'));
    }

    public function edit(Form $form)
    {
        $form->load('fields');
        return view('admin.forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.field_type' => 'required|string',
            'fields.*.field_name' => 'required|string',
            'fields.*.field_label' => 'required|string',
        ]);

        $form->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'settings' => $request->settings ?? [],
        ]);

        // Delete existing fields and create new ones
        $form->fields()->delete();
        
        foreach ($request->fields as $index => $fieldData) {
            FormField::create([
                'form_id' => $form->id,
                'field_type' => $fieldData['field_type'],
                'field_name' => $fieldData['field_name'],
                'field_label' => $fieldData['field_label'],
                'field_description' => $fieldData['field_description'] ?? null,
                'field_options' => $fieldData['field_options'] ?? null,
                'validation_rules' => $fieldData['validation_rules'] ?? [],
                'is_required' => $fieldData['is_required'] ?? false,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully!');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully!');
    }

    public function generateQrCode(Form $form)
    {
        $formUrl = route('public.forms.show', $form->slug);
        
        // Generate QR code
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($formUrl);

        // Save QR code to storage
        $fileName = 'form_' . $form->id . '_' . time() . '.png';
        $filePath = 'qr-codes/' . $fileName;
        
        \Storage::disk('public')->put($filePath, $qrCode);

        // Update form with QR code info
        $form->update([
            'qr_code' => $fileName,
            'qr_code_url' => $formUrl,
        ]);

        return response()->json([
            'success' => true,
            'qr_code_url' => asset('storage/' . $filePath),
            'form_url' => $formUrl,
        ]);
    }

    public function toggleStatus(Form $form)
    {
        $form->update(['is_active' => !$form->is_active]);
        
        return response()->json([
            'success' => true,
            'is_active' => $form->is_active,
        ]);
    }
}