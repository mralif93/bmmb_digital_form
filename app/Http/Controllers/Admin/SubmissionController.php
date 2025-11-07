<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\DarFormSubmission;
use App\Models\DcrFormSubmission;
use App\Models\RafFormSubmission;
use App\Models\SrfFormSubmission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Display DAR submissions
     */
    public function dar()
    {
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'dar')->first();
        if ($form) {
            $submissions = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Fallback to old system
            $submissions = DarFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        }
        return view('admin.submissions.dar', compact('submissions'));
    }

    /**
     * Display DCR submissions
     */
    public function dcr()
    {
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'dcr')->first();
        if ($form) {
            $submissions = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Fallback to old system
            $submissions = DcrFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        }
        return view('admin.submissions.dcr', compact('submissions'));
    }

    /**
     * Display RAF submissions
     */
    public function raf()
    {
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'raf')->first();
        if ($form) {
            $submissions = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Fallback to old system
            $submissions = RafFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        }
        return view('admin.submissions.raf', compact('submissions'));
    }

    /**
     * Display SRF submissions
     */
    public function srf()
    {
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'srf')->first();
        if ($form) {
            $submissions = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Fallback to old system
            $submissions = SrfFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        }
        return view('admin.submissions.srf', compact('submissions'));
    }

    /**
     * Show specific submission details
     */
    public function showDar($id)
    {
        // Try new system first
        $form = Form::where('slug', 'dar')->first();
        if ($form) {
            $submission = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
                ->findOrFail($id);
        } else {
            // Fallback to old system
            $submission = DarFormSubmission::with(['user', 'branch', 'reviewedBy', 'darForm'])->findOrFail($id);
        }
        return view('admin.submissions.show-dar', compact('submission'));
    }

    /**
     * Show specific submission details
     */
    public function showDcr($id)
    {
        // Try new system first
        $form = Form::where('slug', 'dcr')->first();
        if ($form) {
            $submission = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
                ->findOrFail($id);
        } else {
            // Fallback to old system
            $submission = DcrFormSubmission::with(['user', 'branch', 'reviewedBy', 'dcrForm'])->findOrFail($id);
        }
        return view('admin.submissions.show-dcr', compact('submission'));
    }

    /**
     * Show specific submission details
     */
    public function showRaf($id)
    {
        // Try new system first
        $form = Form::where('slug', 'raf')->first();
        if ($form) {
            $submission = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
                ->findOrFail($id);
        } else {
            // Fallback to old system
            $submission = RafFormSubmission::with(['user', 'branch', 'reviewedBy', 'rafForm'])->findOrFail($id);
        }
        return view('admin.submissions.show-raf', compact('submission'));
    }

    /**
     * Show specific submission details
     */
    public function showSrf($id)
    {
        // Try new system first
        $form = Form::where('slug', 'srf')->first();
        if ($form) {
            $submission = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
                ->findOrFail($id);
        } else {
            // Fallback to old system
            $submission = SrfFormSubmission::with(['user', 'branch', 'reviewedBy', 'srfForm'])->findOrFail($id);
        }
        return view('admin.submissions.show-srf', compact('submission'));
    }

    /**
     * Update submission status
     */
    public function updateStatus(Request $request, $type, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed,expired,in_progress,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Try new system first
        $form = Form::where('slug', $type)->first();
        if ($form) {
            $submission = FormSubmission::where('form_id', $form->id)->findOrFail($id);
        } else {
            // Fallback to old system
            $model = match($type) {
                'dar' => DarFormSubmission::class,
                'dcr' => DcrFormSubmission::class,
                'raf' => RafFormSubmission::class,
                'srf' => SrfFormSubmission::class,
                default => throw new \Exception('Invalid submission type'),
            };
            $submission = $model::findOrFail($id);
        }

        $submission->status = $request->status;
        if ($request->has('notes')) {
            $submission->review_notes = $request->notes;
        }
        $submission->save();

        return back()->with('success', 'Submission status updated successfully.');
    }
}
