<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for the submission workflow (Take Up / Complete).
 * 
 * Tests role-based access control for CFE and BM users.
 */
class SubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $cfeUser;
    protected User $bmUser;
    protected User $hqUser;
    protected User $adminUser;
    protected Branch $branch;
    protected Form $form;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a branch
        $this->branch = Branch::factory()->create([
            'branch_name' => 'Test Branch',
            'ti_agent_code' => 'TB001',
        ]);

        // Create a form
        $this->form = Form::factory()->create([
            'name' => 'Test Form',
            'slug' => 'test-form',
            'status' => 'active',
        ]);

        // Create users with different roles
        $this->cfeUser = User::factory()->create([
            'role' => 'cfe',
            'branch_id' => $this->branch->id,
        ]);

        $this->bmUser = User::factory()->create([
            'role' => 'branch_manager',
            'branch_id' => $this->branch->id,
        ]);

        $this->hqUser = User::factory()->create([
            'role' => 'headquarters',
            'branch_id' => null,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'branch_id' => null,
        ]);
    }

    /**
     * Test CFE can take up a submitted submission.
     */
    public function test_cfe_can_take_up_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals('pending_process', $submission->status);
        $this->assertEquals($this->cfeUser->id, $submission->taken_up_by);
        $this->assertNotNull($submission->taken_up_at);
    }

    /**
     * Test BM can take up a submitted submission.
     */
    public function test_bm_can_take_up_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->bmUser)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals('pending_process', $submission->status);
        $this->assertEquals($this->bmUser->id, $submission->taken_up_by);
    }

    /**
     * Test HQ cannot take up a submission.
     */
    public function test_hq_cannot_take_up_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->hqUser)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertForbidden();

        $submission->refresh();
        $this->assertEquals('submitted', $submission->status);
    }

    /**
     * Test Admin cannot take up a submission (workflow is for CFE/BM).
     */
    public function test_admin_cannot_take_up_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertForbidden();

        $submission->refresh();
        $this->assertEquals('submitted', $submission->status);
    }

    /**
     * Test CFE can complete a pending_process submission.
     */
    public function test_cfe_can_complete_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending_process',
            'taken_up_by' => $this->cfeUser->id,
            'taken_up_at' => now(),
        ]);

        $response = $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.complete', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals('completed', $submission->status);
        $this->assertEquals($this->cfeUser->id, $submission->completed_by);
        $this->assertNotNull($submission->completed_at);
    }

    /**
     * Test BM can complete a pending_process submission.
     */
    public function test_bm_can_complete_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending_process',
            'taken_up_by' => $this->bmUser->id,
            'taken_up_at' => now(),
        ]);

        $response = $this->actingAs($this->bmUser)
            ->post(route('admin.submissions.complete', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals('completed', $submission->status);
        $this->assertEquals($this->bmUser->id, $submission->completed_by);
    }

    /**
     * Test HQ cannot complete a submission.
     */
    public function test_hq_cannot_complete_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending_process',
            'taken_up_by' => $this->cfeUser->id,
            'taken_up_at' => now(),
        ]);

        $response = $this->actingAs($this->hqUser)
            ->post(route('admin.submissions.complete', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $response->assertForbidden();

        $submission->refresh();
        $this->assertEquals('pending_process', $submission->status);
    }

    /**
     * Test cannot take up a submission that is not in 'submitted' status.
     */
    public function test_cannot_take_up_non_submitted_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'completed', // Already completed
        ]);

        $response = $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        // Should get an error (either 422 or redirect with error)
        $submission->refresh();
        $this->assertEquals('completed', $submission->status);
    }

    /**
     * Test cannot complete a submission that is not in 'pending_process' status.
     */
    public function test_cannot_complete_non_pending_process_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'submitted', // Not yet taken up
        ]);

        $response = $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.complete', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        // Should get an error
        $submission->refresh();
        $this->assertEquals('submitted', $submission->status);
    }

    /**
     * Test CFE from different branch cannot take up submission.
     */
    public function test_cfe_cannot_take_up_submission_from_different_branch(): void
    {
        $otherBranch = Branch::factory()->create([
            'branch_name' => 'Other Branch',
            'ti_agent_code' => 'OB001',
        ]);

        $otherCfe = User::factory()->create([
            'role' => 'cfe',
            'branch_id' => $otherBranch->id,
        ]);

        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id, // Different branch
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($otherCfe)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        // Should be forbidden or redirect with error
        $submission->refresh();
        $this->assertEquals('submitted', $submission->status);
    }

    /**
     * Test workflow flow: Submit -> Take Up -> Complete.
     */
    public function test_full_workflow_submit_takeup_complete(): void
    {
        // Create a submitted submission
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'submitted',
        ]);

        // Step 1: CFE takes up the submission
        $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.take-up', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $submission->refresh();
        $this->assertEquals('pending_process', $submission->status);
        $this->assertEquals($this->cfeUser->id, $submission->taken_up_by);

        // Step 2: CFE completes the submission
        $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.complete', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]));

        $submission->refresh();
        $this->assertEquals('completed', $submission->status);
        $this->assertEquals($this->cfeUser->id, $submission->completed_by);
        $this->assertNotNull($submission->completed_at);
    }

    /**
     * Test completion notes are saved.
     */
    public function test_completion_notes_are_saved(): void
    {
        $submission = FormSubmission::factory()->create([
            'form_id' => $this->form->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending_process',
            'taken_up_by' => $this->cfeUser->id,
            'taken_up_at' => now(),
        ]);

        $notes = 'Customer verified and documents processed successfully.';

        $response = $this->actingAs($this->cfeUser)
            ->post(route('admin.submissions.complete', [
                'formSlug' => $this->form->slug,
                'id' => $submission->id,
            ]), [
                'completion_notes' => $notes,
            ]);

        $submission->refresh();
        $this->assertEquals('completed', $submission->status);
        $this->assertEquals($notes, $submission->completion_notes);
    }
}
