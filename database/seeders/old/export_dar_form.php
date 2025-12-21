<?php

/**
 * Export DAR Form Structure to JSON
 * 
 * This script exports the current DAR form structure from the database
 * to exports/dar_form_export.json for use in the FormManagementSeeder.
 * 
 * Usage:
 *   php database/seeders/export_dar_form.php
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Form;

$form = Form::where('slug', 'dar')->with(['sections.fields' => function($q) { 
    $q->orderBy('sort_order'); 
}])->first();

if (!$form) {
    echo "Error: DAR form not found in database.\n";
    echo "Please seed the form first by uncommenting seedDarForm() in FormManagementSeeder.\n";
    exit(1);
}

$export = [
    'form' => [
        'id' => $form->id,
        'name' => $form->name,
        'slug' => $form->slug,
        'description' => $form->description,
        'status' => $form->status,
        'is_public' => $form->is_public,
        'allow_multiple_submissions' => $form->allow_multiple_submissions,
        'submission_limit' => $form->submission_limit,
        'sort_order' => $form->sort_order,
        'settings' => $form->settings,
    ],
    'sections' => []
];

foreach ($form->sections as $section) {
    $sectionData = [
        'id' => $section->id,
        'key' => $section->section_key,
        'title' => $section->section_title ?? null,
        'description' => $section->section_label ?? $section->section_description ?? null,
        'sort_order' => $section->sort_order,
        'is_active' => $section->is_active,
        'fields' => []
    ];
    
    foreach ($section->fields as $field) {
        $fieldData = [
            'name' => $field->field_name,
            'label' => $field->field_label,
            'type' => $field->field_type,
            'placeholder' => $field->field_placeholder,
            'description' => $field->field_description,
            'help_text' => $field->field_help_text,
            'required' => $field->is_required,
            'active' => $field->is_active,
            'sort_order' => $field->sort_order,
            'grid_column' => $field->grid_column,
            'conditional' => $field->is_conditional,
            'conditional_logic' => $field->conditional_logic,
            'options' => $field->field_options,
            'settings' => $field->field_settings,
            'validation' => $field->validation_rules,
        ];
        $sectionData['fields'][] = $fieldData;
    }
    
    $export['sections'][] = $sectionData;
}

$jsonFile = __DIR__ . '/exports/dar_form_export.json';
$jsonContent = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (file_put_contents($jsonFile, $jsonContent) === false) {
    echo "Error: Could not write to {$jsonFile}\n";
    exit(1);
}

$totalFields = array_sum(array_map(function($s) { return count($s['fields']); }, $export['sections']));

echo "✓ DAR form exported successfully!\n";
echo "  File: {$jsonFile}\n";
echo "  Form: {$form->name}\n";
echo "  Sections: " . count($export['sections']) . "\n";
echo "  Total fields: {$totalFields}\n";
echo "\n";
echo "Field types breakdown:\n";
$fieldTypes = [];
foreach ($export['sections'] as $section) {
    foreach ($section['fields'] as $field) {
        $fieldTypes[$field['type']] = ($fieldTypes[$field['type']] ?? 0) + 1;
    }
}
foreach ($fieldTypes as $type => $count) {
    echo "  - {$type}: {$count}\n";
}
echo "\n";
echo "To update the seeder, run:\n";
echo "  php artisan db:seed --class=FormManagementSeeder\n";

