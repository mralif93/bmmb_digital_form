<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class FormSeeder extends Seeder
{
    /**
     * Seed forms from JSON exports.
     */
    public function run(): void
    {
        $this->command->info('Seeding forms from exports...');

        $forms = [
            'dar' => [
                'name' => 'DAR Form',
                'slug' => 'dar',
                'description' => 'Daily Activity Report Form',
                'export_file' => 'dar_form_export.json',
                'sort_order' => 1,
            ],
            'dcr' => [
                'name' => 'DCR Form',
                'slug' => 'dcr',
                'description' => 'Daily Collection Report Form',
                'export_file' => 'dcr_form_export.json',
                'sort_order' => 2,
            ],
            'srf' => [
                'name' => 'SRF Form',
                'slug' => 'srf',
                'description' => 'Service Request Form',
                'export_file' => 'srf_form_export.json',
                'sort_order' => 3,
            ],
        ];

        foreach ($forms as $formKey => $formData) {
            $this->seedForm($formData);
        }
    }

    /**
     * Seed a single form from its export file.
     */
    private function seedForm(array $formData): void
    {
        $exportPath = database_path("seeders/exports/{$formData['export_file']}");

        if (!file_exists($exportPath)) {
            $this->command->warn("Export file not found: {$formData['export_file']}");
            return;
        }

        try {
            $exportData = json_decode(file_get_contents($exportPath), true);

            if (!$exportData || !isset($exportData['form'])) {
                $this->command->warn("Invalid export format: {$formData['export_file']}");
                return;
            }

            // Create or update form
            $form = Form::updateOrCreate(
                ['slug' => $formData['slug']],
                [
                    'name' => $formData['name'],
                    'description' => $formData['description'],
                    'status' => 'active',
                    'sort_order' => $formData['sort_order'],
                    'settings' => $exportData['form']['settings'] ?? null,
                ]
            );

            $this->command->info("  ✓ Form: {$form->name}");

            // Use transaction with raw SQL to ensure proper deletion in SQLite
            \DB::transaction(function () use ($form, $exportData) {
                // Clear existing sections and fields using raw SQL for immediate deletion
                \DB::statement('DELETE FROM form_fields WHERE form_id = ?', [$form->id]);
                \DB::statement('DELETE FROM form_sections WHERE form_id = ?', [$form->id]);

                // Import sections
                if (isset($exportData['sections'])) {
                    $sectionCount = 0;
                    $fieldCount = 0;

                    foreach ($exportData['sections'] as $sectionData) {
                        // Handle both formats: key/title OR section_key/section_label
                        $sectionKey = $sectionData['key'] ?? $sectionData['section_key'] ?? 'section_' . ($sectionCount + 1);
                        $sectionLabel = $sectionData['title'] ?? $sectionData['section_label'] ?? 'Section ' . ($sectionCount + 1);

                        $section = FormSection::create([
                            'form_id' => $form->id,
                            'section_key' => $sectionKey,
                            'section_label' => $sectionLabel,
                            'section_description' => $sectionData['description'] ?? $sectionData['section_description'] ?? null,
                            'sort_order' => $sectionData['sort_order'] ?? $sectionCount + 1,
                            'is_active' => $sectionData['is_active'] ?? true,
                            'grid_layout' => $sectionData['grid_layout'] ?? '2-column',
                        ]);
                        $sectionCount++;

                        // Import fields
                        if (isset($sectionData['fields'])) {
                            foreach ($sectionData['fields'] as $fieldData) {
                                FormField::create([
                                    'form_id' => $form->id,
                                    'section_id' => $section->id,
                                    'field_name' => $fieldData['name'] ?? $fieldData['field_name'],
                                    'field_label' => $fieldData['label'] ?? $fieldData['field_label'],
                                    'field_description' => $fieldData['description'] ?? $fieldData['field_description'] ?? null,
                                    'field_type' => $fieldData['type'] ?? $fieldData['field_type'],
                                    'field_placeholder' => $fieldData['placeholder'] ?? $fieldData['field_placeholder'] ?? null,
                                    'field_help_text' => $fieldData['help_text'] ?? $fieldData['field_help_text'] ?? null,
                                    'is_required' => $fieldData['required'] ?? $fieldData['is_required'] ?? false,
                                    'is_conditional' => $fieldData['conditional'] ?? $fieldData['is_conditional'] ?? false,
                                    'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                                    'validation_rules' => $fieldData['validation'] ?? $fieldData['validation_rules'] ?? null,
                                    'field_options' => $fieldData['options'] ?? $fieldData['field_options'] ?? null,
                                    'field_settings' => $fieldData['settings'] ?? $fieldData['field_settings'] ?? null,
                                    'sort_order' => $fieldData['sort_order'] ?? $fieldCount + 1,
                                    'grid_column' => $fieldData['grid_column'] ?? 'full',
                                    'is_active' => $fieldData['active'] ?? $fieldData['is_active'] ?? true,
                                ]);
                                $fieldCount++;
                            }
                        }
                    }

                    $this->command->info("    - {$sectionCount} sections, {$fieldCount} fields");
                }
            });

        } catch (\Exception $e) {
            $this->command->error("Error seeding {$formData['name']}: " . $e->getMessage());
            Log::error('Form seeding error', [
                'form' => $formData['slug'],
                'error' => $e->getMessage()
            ]);
        }
    }
}
