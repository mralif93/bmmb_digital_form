<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Get the DCR form
        $form = DB::table('forms')->where('slug', 'dcr')->first();

        if (!$form) {
            echo "DCR form not found!\n";
            return;
        }

        // Get Section D (Correction section)
        $section = DB::table('form_sections')
            ->where('form_id', $form->id)
            ->where('section_key', 'section_d')
            ->first();

        if (!$section) {
            echo "Section D not found!\n";
            return;
        }

        // Insert Account Type 3 field
        DB::table('form_fields')->insert([
            'form_id' => $form->id,
            'section_id' => $section->id,
            'field_name' => 'field_4_6_1',
            'field_label' => 'Account Type 3',
            'field_type' => 'text',
            'field_placeholder' => 'Enter account type (optional)',
            'field_description' => '<p><br></p>',
            'field_help_text' => null,
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 5.5,
            'grid_column' => 'left',
            'conditional_logic' => json_encode([
                'action' => 'show_if',
                'logic' => 'and',
                'conditions' => [
                    [
                        'field' => 'field_4_1',
                        'operator' => 'equals',
                        'value' => 'specific'
                    ]
                ]
            ]),
            'field_options' => null,
            'field_settings' => json_encode(['description_position' => 'bottom']),
            'validation_rules' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Account No. 3 field
        DB::table('form_fields')->insert([
            'form_id' => $form->id,
            'section_id' => $section->id,
            'field_name' => 'field_4_6_2',
            'field_label' => 'Account No. 3',
            'field_type' => 'text',
            'field_placeholder' => 'Enter account number (optional)',
            'field_description' => '<p><br></p>',
            'field_help_text' => null,
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 5.6,
            'grid_column' => 'right',
            'conditional_logic' => json_encode([
                'action' => 'show_if',
                'logic' => 'and',
                'conditions' => [
                    [
                        'field' => 'field_4_1',
                        'operator' => 'equals',
                        'value' => 'specific'
                    ]
                ]
            ]),
            'field_options' => null,
            'field_settings' => json_encode(['description_position' => 'bottom']),
            'validation_rules' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Successfully added Account Type 3 and Account No. 3 fields!\n";
    }

    public function down()
    {
        DB::table('form_fields')
            ->whereIn('field_name', ['field_4_6_1', 'field_4_6_2'])
            ->delete();
    }
};
