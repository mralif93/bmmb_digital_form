<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
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
     * Display public form
     */
    public function show($type, $branch = null)
    {
        if (!isset($this->formModelMap[$type])) {
            abort(404);
        }

        // Handle branch linking
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }

        // Get the first active form (or first available)
        $formModel = $this->formModelMap[$type];
        $form = $formModel::where('status', '!=', 'draft')->first();
        
        if (!$form) {
            $form = $formModel::first();
        }

        if (!$form) {
            return view('public.forms.' . $type)->with('error', 'No form available. Please contact administrator.');
        }

        // Ensure sections are initialized
        \App\Models\FormSection::initializeDefaults($type);

        // Get form renderer service
        $formRenderer = app(FormRendererService::class);
        $formHtml = $formRenderer->renderForm($form->id, $type);
        $sections = $formRenderer->getSections($form->id, $type);

        return view('public.forms.dynamic', compact('form', 'type', 'formHtml', 'sections'));
    }
}

