<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\FormRendererService;
use Illuminate\Http\Request;

class FormController extends Controller
{

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

        // Check if branch ID is present in session
        if (!session('submission_branch_id')) {
            abort(403, 'Access Restricted. Please scan the Branch QR Code to access this form.');
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
     * Display public form (uses new form management system)
     */
    public function show($type, $branch = null)
    {
        // Handle branch linking
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }

        // Check if branch ID is present in session
        if (!session('submission_branch_id')) {
            abort(403, 'Access Restricted. Please scan the Branch QR Code to access this form.');
        }

        // Get form from new Form model (required - no fallback to old system)
        $form = Form::where('slug', $type)
            ->where('status', 'active')
            ->where('is_public', true)
            ->first();

        if (!$form) {
            abort(404, 'Form not found or not available. Please contact administrator.');
        }

        // Get form type from settings or slug
        $formType = $form->settings['type'] ?? $form->slug;

        // Use new form management system with FormRendererService
        $formRenderer = app(FormRendererService::class);
        $formHtml = $formRenderer->renderForm($form->id, $formType);
        $sections = $formRenderer->getSections($form->id, $formType);

        return view('public.forms.dynamic', compact('form', 'type', 'formHtml', 'sections'));
    }
}

