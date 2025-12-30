<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Add Email Address field to DCR form for data correction
     */
    public function up(): void
    {
        // Get the DCR form ID
        $dcrForm = DB::table('forms')->where('slug', 'dcr')->first();

        if (!$dcrForm) {
            return; // DCR form not found, skip migration
        }

        // Get the section_d (Personal Data Correction) section ID
        $section = DB::table('form_sections')
            ->where('form_id', $dcrForm->id)
            ->where('section_key', 'section_d')
            ->first();

        if (!$section) {
            return; // Section not found, skip migration
        }

        // Check if fields already exist
        $existingField = DB::table('form_fields')
            ->where('section_id', $section->id)
            ->where('field_name', 'field_4_35')
            ->first();

        if ($existingField) {
            return; // Fields already exist, skip
        }

        // Insert Email Address field
        DB::table('form_fields')->insert([
            'form_id' => $dcrForm->id,
            'section_id' => $section->id,
            'field_name' => 'field_4_35',
            'field_label' => 'Email Address',
            'field_type' => 'text',
            'field_placeholder' => 'Enter corrected email address',
            'field_description' => '<p><br></p>',
            'field_help_text' => null,
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 26.5,
            'grid_column' => 'left',
            'is_conditional' => false,
            'conditional_logic' => null,
            'field_options' => null,
            'field_settings' => json_encode(['description_position' => 'bottom']),
            'validation_rules' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Action for Email field
        DB::table('form_fields')->insert([
            'form_id' => $dcrForm->id,
            'section_id' => $section->id,
            'field_name' => 'field_4_36',
            'field_label' => 'Action for Email',
            'field_type' => 'radio',
            'field_placeholder' => null,
            'field_description' => '<p><br></p>',
            'field_help_text' => null,
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 26.6,
            'grid_column' => 'right',
            'is_conditional' => false,
            'conditional_logic' => null,
            'field_options' => json_encode(['A' => 'Add', 'D' => 'Delete', 'R' => 'Revise']),
            'field_settings' => json_encode(['description_position' => 'bottom']),
            'validation_rules' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the DCR form ID
        $dcrForm = DB::table('forms')->where('slug', 'dcr')->first();

        if (!$dcrForm) {
            return;
        }

        $section = DB::table('form_sections')
            ->where('form_id', $dcrForm->id)
            ->where('section_key', 'section_d')
            ->first();

        if (!$section) {
            return;
        }

        // Remove the Email Address fields
        DB::table('form_fields')
            ->where('section_id', $section->id)
            ->whereIn('field_name', ['field_4_35', 'field_4_36'])
            ->delete();
    }
};
