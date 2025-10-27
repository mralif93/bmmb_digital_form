<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $submissions = DarFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.submissions.dar', compact('submissions'));
    }

    /**
     * Display DCR submissions
     */
    public function dcr()
    {
        $submissions = DcrFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.submissions.dcr', compact('submissions'));
    }

    /**
     * Display RAF submissions
     */
    public function raf()
    {
        $submissions = RafFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.submissions.raf', compact('submissions'));
    }

    /**
     * Display SRF submissions
     */
    public function srf()
    {
        $submissions = SrfFormSubmission::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.submissions.srf', compact('submissions'));
    }

    /**
     * Show specific submission details
     */
    public function showDar($id)
    {
        $submission = DarFormSubmission::with('user')->findOrFail($id);
        return view('admin.submissions.show-dar', compact('submission'));
    }

    /**
     * Show specific submission details
     */
    public function showDcr($id)
    {
        $submission = DcrFormSubmission::with('user')->findOrFail($id);
        return view('admin.submissions.show-dcr', compact('submission'));
    }

    /**
     * Show specific submission details
     */
    public function showRaf($id)
    {
        $submission = RafFormSubmission::with('user')->findOrFail($id);
        return view('admin.submissions.show-raf', compact('submission'));
    }

    /**
     * Show specific submission details
     */
    public function showSrf($id)
    {
        $submission = SrfFormSubmission::with('user')->findOrFail($id);
        return view('admin.submissions.show-srf', compact('submission'));
    }

    /**
     * Update submission status
     */
    public function updateStatus(Request $request, $type, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed,expired',
            'notes' => 'nullable|string',
        ]);

        $model = match($type) {
            'dar' => DarFormSubmission::class,
            'dcr' => DcrFormSubmission::class,
            'raf' => RafFormSubmission::class,
            'srf' => SrfFormSubmission::class,
            default => throw new \Exception('Invalid submission type'),
        };

        $submission = $model::findOrFail($id);
        $submission->status = $request->status;
        if ($request->has('notes')) {
            $submission->review_notes = $request->notes;
        }
        $submission->save();

        return back()->with('success', 'Submission status updated successfully.');
    }
}
