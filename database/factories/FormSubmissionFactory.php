<?php

namespace Database\Factories;

use App\Models\FormSubmission;
use App\Models\Form;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormSubmission>
 */
class FormSubmissionFactory extends Factory
{
    protected $model = FormSubmission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'branch_id' => Branch::factory(),
            'user_id' => User::factory(),
            'submission_token' => Str::uuid()->toString(),
            'reference_number' => 'REF-' . strtoupper(Str::random(8)),
            'status' => 'submitted',
            'field_responses' => [],
            'submission_data' => [],
            'submitted_at' => now(),
        ];
    }

    /**
     * Indicate that the submission is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
        ]);
    }

    /**
     * Indicate that the submission is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Indicate that the submission is pending process (taken up).
     */
    public function pendingProcess(User $takenUpBy = null): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending_process',
            'taken_up_by' => $takenUpBy?->id ?? User::factory(),
            'taken_up_at' => now(),
        ]);
    }

    /**
     * Indicate that the submission is completed.
     */
    public function completed(User $completedBy = null): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'completed_by' => $completedBy?->id ?? User::factory(),
            'completed_at' => now(),
        ]);
    }

    /**
     * Indicate that the submission is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the submission is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}
