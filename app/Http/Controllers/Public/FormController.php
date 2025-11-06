<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\Form;
use App\Services\FormRendererService;
use Illuminate\Http\Request;

class FormController extends Controller
{
    private $formModelMap = [
        'raf' => RemittanceApplicationForm::class,
        'dar' => DataAccessRequestForm::class,
        'dcr' => DataCorrectionRequestForm::class,
        'srf' => ServiceRequestForm::class,
    ];

    /**
     * Display public form by slug (new form management system)
     */
    public function showBySlug($slug, $branch = null)
    {
        // Handle branch linking
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }

        // Get form from new Form model
        $form = Form::where('slug', $slug)
            ->where('status', 'active')
            ->where('is_public', true)
            ->first();

        if (!$form) {
            abort(404, 'Form not found or not available.');
        }

        // Get form type from settings or slug
        $type = $form->settings['type'] ?? $form->slug;

        // Get form renderer service
        $formRenderer = app(FormRendererService::class);
        $formHtml = $formRenderer->renderForm($form->id, $type);
        $sections = $formRenderer->getSections($form->id, $type);

        return view('public.forms.dynamic', compact('form', 'type', 'formHtml', 'sections'));
    }

    /**
     * Display public form (legacy method for backward compatibility)
     */
    public function show($type, $branch = null)
    {
        if (!isset($this->formModelMap[$type])) {
            // Try to find form by slug in new Form model
            $form = Form::where('slug', $type)
                ->where('status', 'active')
                ->where('is_public', true)
                ->first();
            
            if ($form) {
                return $this->showBySlug($type, $branch);
            }
            
            abort(404);
        }

        // Handle branch linking
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }

        // Try to get form from new Form model first
        $form = Form::where('slug', $type)
            ->where('status', 'active')
            ->where('is_public', true)
            ->first();

        if ($form) {
            // Use new form management system
            $formRenderer = app(FormRendererService::class);
            $formHtml = $formRenderer->renderForm($form->id, $type);
            $sections = $formRenderer->getSections($form->id, $type);
            
            return view('public.forms.dynamic', compact('form', 'type', 'formHtml', 'sections'));
        }

        // Fallback to old form models for backward compatibility
        $formModel = $this->formModelMap[$type];
        $oldForm = $formModel::where('status', '!=', 'draft')->first();
        
        if (!$oldForm) {
            $oldForm = $formModel::first();
        }

        if (!$oldForm) {
            return view('public.forms.' . $type)->with('error', 'No form available. Please contact administrator.');
        }

        // Ensure sections are initialized
        \App\Models\FormSection::initializeDefaults($oldForm->id, $type);

        // Get form renderer service
        $formRenderer = app(FormRendererService::class);
        $formHtml = $formRenderer->renderForm($oldForm->id, $type);
        $sections = $formRenderer->getSections($oldForm->id, $type);

        // Create a temporary form object for the view
        $form = (object) [
            'id' => $oldForm->id,
            'name' => $oldForm->name ?? ucfirst($type) . ' Form',
            'slug' => $type,
            'description' => null,
        ];

        return view('public.forms.dynamic', compact('form', 'type', 'formHtml', 'sections'));
    }
}

