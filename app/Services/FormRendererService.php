<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;

class FormRendererService
{

    /**
     * Render a complete form dynamically from database fields
     */
    public function renderForm($formId, $formType = 'raf'): string
    {
        // Get form to verify it exists
        $form = Form::find($formId);
        if (!$form) {
            return '<p class="text-gray-500">Form not found. Please contact administrator.</p>';
        }

        // Use new FormField model
        $fields = FormField::where('form_id', $formId)
            ->where('is_active', true)
            ->with('section')
            ->ordered()
            ->get();

        if ($fields->isEmpty()) {
            return '<p class="text-gray-500">No form fields configured. Please configure fields in admin panel.</p>';
        }

        // Ensure sections are initialized
        FormSection::initializeDefaults($formId, $formType);

        // Group fields by section
        $sections = $fields->groupBy(function ($field) {
            return $field->section ? $field->section->section_key : 'other';
        });

        // Get sections from database
        $dbSections = FormSection::forForm($formId)
            ->ordered()
            ->get()
            ->keyBy('section_key');

        // Sort sections by database sort_order
        // Include ALL sections that have fields, regardless of active status
        // Build an array with sort_order as key, then sort by key
        $sectionsWithOrder = [];
        foreach ($sections as $sectionName => $sectionFields) {
            $dbSection = $dbSections->get($sectionName);
            // Include all sections that have fields, order by database sort_order
            $sortOrder = $dbSection ? $dbSection->sort_order : 999;

            // Handle duplicate sort_order by appending a small increment
            $baseSortOrder = $sortOrder;
            $increment = 0;
            while (isset($sectionsWithOrder[$sortOrder])) {
                $increment += 0.001;
                $sortOrder = $baseSortOrder + $increment;
            }

            $sectionsWithOrder[$sortOrder] = [
                'name' => $sectionName,
                'fields' => $sectionFields,
            ];
        }
        ksort($sectionsWithOrder);

        $html = '';
        $stepIndex = 1;
        foreach ($sectionsWithOrder as $sectionData) {
            $html .= $this->renderSection($sectionData['name'], $sectionData['fields'], $formType, $stepIndex);
            $stepIndex++;
        }

        return $html;
    }

    /**
     * Get sections information for stepper
     */
    public function getSections($formId, $formType = 'raf'): array
    {
        // Get form to verify it exists
        $form = Form::find($formId);
        if (!$form) {
            return [];
        }

        // Use new FormField model
        $fields = FormField::where('form_id', $formId)
            ->where('is_active', true)
            ->with('section')
            ->ordered()
            ->get();

        if ($fields->isEmpty()) {
            return [];
        }

        // Ensure sections are initialized
        FormSection::initializeDefaults($formId, $formType);

        // Group fields by section
        $sections = $fields->groupBy(function ($field) {
            return $field->section ? $field->section->section_key : 'other';
        });

        // Get sections from database
        $dbSections = FormSection::forForm($formId)
            ->ordered()
            ->get()
            ->keyBy('section_key');

        $sectionsData = [];
        $stepIndex = 1;

        // Sort sections by database sort_order
        // Include ALL sections that have fields, regardless of active status
        // Build an array with sort_order as key, then sort by key
        $sectionsWithOrder = [];
        foreach ($sections as $sectionName => $sectionFields) {
            $dbSection = $dbSections->get($sectionName);
            // Include all sections that have fields, order by database sort_order
            $sortOrder = $dbSection ? $dbSection->sort_order : 999;

            // Handle duplicate sort_order by appending a small increment
            $baseSortOrder = $sortOrder;
            $increment = 0;
            while (isset($sectionsWithOrder[$sortOrder])) {
                $increment += 0.001;
                $sortOrder = $baseSortOrder + $increment;
            }

            $sectionsWithOrder[$sortOrder] = [
                'name' => $sectionName,
                'fields' => $sectionFields,
            ];
        }
        ksort($sectionsWithOrder);

        foreach ($sectionsWithOrder as $sectionData) {
            $dbSection = $dbSections->get($sectionData['name']);
            // Get section label from database section if available
            $sectionLabel = $dbSection ? $dbSection->section_label : $this->getSectionLabel($sectionData['name']);
            $sectionsData[] = [
                'name' => $sectionData['name'],
                'label' => $sectionLabel,
                'step' => $stepIndex,
                'sort_order' => $dbSection ? $dbSection->sort_order : 999,
            ];
            $stepIndex++;
        }

        return $sectionsData;
    }

    /**
     * Render a section with its fields
     */
    private function renderSection(string $sectionName, $fields, string $formType, int $stepIndex = 1): string
    {
        // Try to get section label and grid layout from FormSection model first
        $sectionLabel = null;
        $gridLayout = '2-column';

        if (class_exists(\App\Models\FormSection::class)) {
            $section = \App\Models\FormSection::where('section_key', $sectionName)->first();
            if ($section) {
                $sectionLabel = $section->section_label;
                $gridLayout = $section->grid_layout ?? '2-column';
            }
        }

        // Fallback to default label method
        if (!$sectionLabel) {
            $sectionLabel = $this->getSectionLabel($sectionName);
        }

        $html = '<div class="form-step" data-section="' . htmlspecialchars($sectionName) . '" data-step="' . $stepIndex . '" x-show="currentStep === ' . $stepIndex . '" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform -translate-x-4">';

        // Use 6-column grid for Personal Data Correction section (section_d) to allow wider inputs + narrower actions
        // Use standard 2-column grid for all other sections
        $gridCols = ($sectionName === 'section_d') ? 'md:grid-cols-6' : 'md:grid-cols-2';
        $html .= '<div class="grid grid-cols-1 ' . $gridCols . ' gap-4">';

        foreach ($fields as $field) {
            $html .= $this->renderField($field, null, $fields, $sectionName);
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render a single field based on its type
     */
    public function renderField($field, $columnPosition = null, $allFields = null, $sectionName = null): string
    {
        $conditionalData = '';
        $isConditional = false;
        $isTriggeredByCheckbox = false;

        // Check if field is conditional - check if conditional_logic data exists
        // Don't rely solely on is_conditional flag as it may not be set correctly during import
        if (!empty($field->conditional_logic) && is_array($field->conditional_logic)) {
            $conditionalData = $this->buildConditionalAttributes($field->conditional_logic);
            $isConditional = true;

            // Check if this conditional field is triggered by a checkbox
            if ($allFields) {
                $isTriggeredByCheckbox = $this->isTriggeredByCheckbox($field, $allFields);
            }
        }

        // Determine column span based on grid_column
        // Respect admin-configured grid column. Only default to full width when
        // no column was set and the field type typically spans both columns.
        $gridColumn = $field->grid_column ?: null;
        if (!$gridColumn) {
            $fullWidthTypes = ['textarea', 'radio', 'checkbox', 'file'];
            if (
                in_array($field->field_type, $fullWidthTypes) ||
                (in_array($field->field_type, ['radio', 'checkbox']) && method_exists($field, 'hasOptions') && $field->hasOptions())
            ) {
                $gridColumn = 'full';
            } else {
                $gridColumn = 'left';
            }
        }

        // Apply column span based on grid context
        // section_d uses 6-column grid: full=6, left=4, right=2
        // Other sections use 2-column grid: full=2, left=1, right=1
        $is6ColumnGrid = ($sectionName === 'section_d');

        if ($gridColumn === 'full') {
            $colSpanClass = $is6ColumnGrid ? 'md:col-span-6' : 'md:col-span-2';
        } elseif ($gridColumn === 'left') {
            $colSpanClass = $is6ColumnGrid ? 'md:col-span-4' : '';
        } elseif ($gridColumn === 'right') {
            $colSpanClass = $is6ColumnGrid ? 'md:col-span-2' : '';
        } else {
            $colSpanClass = '';
        }

        // For ALL conditional fields, add pl-7 to form-field container to align with checkbox labels
        // Checkbox width (20px) + ml-2 margin (8px) = 28px (pl-7)
        $paddingClass = $isConditional ? ' pl-7' : '';

        // For conditional fields, add inline style to hide by default (will be shown/hidden by JavaScript)
        $styleAttr = $isConditional ? ' style="display: none;"' : '';

        // Notes field - always full width, wrap with grid column class
        if ($field->field_type === 'notes') {
            // Force full width for notes fields
            $notesColSpanClass = $is6ColumnGrid ? 'md:col-span-6' : 'md:col-span-2';
            return '<div class="form-field ' . $notesColSpanClass . '" data-field-name="' . $field->field_name . '" data-grid-column="full">' . $this->renderNotes($field) . '</div>';
        }

        $html = '<div class="form-field ' . $colSpanClass . $paddingClass . '" data-field-name="' . $field->field_name . '" data-grid-column="' . $gridColumn . '" ' . $conditionalData . $styleAttr . '>';

        switch ($field->field_type) {
            case 'text':
            case 'email':
            case 'phone':
            case 'number':
                $html .= $this->renderTextInput($field, $isTriggeredByCheckbox);
                break;
            case 'textarea':
                $html .= $this->renderTextarea($field, $isTriggeredByCheckbox);
                break;
            case 'select':
                $html .= $this->renderSelect($field, $isTriggeredByCheckbox);
                break;
            case 'radio':
                $html .= $this->renderRadio($field);
                break;
            case 'checkbox':
                // If checkbox is conditional, don't align with triggering checkbox label - align with regular fields
                $html .= $this->renderCheckbox($field, $isConditional);
                break;
            case 'date':
                $html .= $this->renderDate($field, $isTriggeredByCheckbox);
                break;
            case 'file':
                $html .= $this->renderFile($field);
                break;
            case 'currency':
                $html .= $this->renderCurrency($field, $isTriggeredByCheckbox);
                break;
            case 'signature':
                $html .= $this->renderSignature($field, $isTriggeredByCheckbox);
                break;
            case 'repeater':
                $html .= $this->renderRepeater($field, $isTriggeredByCheckbox);
                break;
            default:
                $html .= $this->renderTextInput($field, $isTriggeredByCheckbox);
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render text input field
     */
    private function renderTextInput($field, $isConditional = false): string
    {
        $inputType = in_array($field->field_type, ['email', 'phone', 'number'])
            ? $field->field_type
            : 'text';

        // Padding is applied at form-field container level for conditional fields
        $html = '<div class="form-group">';
        // Enhanced label with better visual hierarchy
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        // Professional input styling with enhanced focus states
        $html .= '<input type="' . $inputType . '" ';
        $html .= 'id="' . $field->field_name . '" ';
        $html .= 'name="' . $field->field_name . '" ';
        if ($field->field_placeholder) {
            $html .= 'placeholder="' . htmlspecialchars($field->field_placeholder) . '" ';
        }
        $html .= 'class="form-input w-full px-3 py-2 border-2 border-gray-200 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-xs transition-all duration-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:shadow-lg hover:border-gray-300 focus:outline-none shadow-sm" ';
        if ($field->is_required) {
            $html .= 'required aria-required="true" ';
        }
        $html .= $this->buildValidationAttributes($field);
        $html .= $this->buildCustomAttributes($field);
        $html .= '/>';

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render textarea field
     */
    private function renderTextarea($field, $isConditional = false): string
    {
        // Padding is applied at form-field container level for conditional fields
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        $html .= '<textarea ';
        $html .= 'id="' . $field->field_name . '" ';
        $html .= 'name="' . $field->field_name . '" ';
        $html .= 'rows="' . ($field->field_settings['rows'] ?? 4) . '" ';
        if ($field->field_placeholder) {
            $html .= 'placeholder="' . htmlspecialchars($field->field_placeholder) . '" ';
        }
        $html .= 'class="form-input w-full px-3 py-2 border-2 border-gray-200 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-xs transition-all duration-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:shadow-lg hover:border-gray-300 resize-y focus:outline-none shadow-sm" ';
        if ($field->is_required) {
            $html .= 'required aria-required="true" ';
        }
        $html .= $this->buildValidationAttributes($field);
        $html .= $this->buildCustomAttributes($field);
        $html .= '>';
        $html .= '</textarea>';

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render select dropdown field
     */
    private function renderSelect($field, $isConditional = false): string
    {
        // Padding is applied at form-field container level for conditional fields
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        $html .= '<div class="relative">';
        $html .= '<select ';
        $html .= 'id="' . $field->field_name . '" ';
        $html .= 'name="' . $field->field_name . '" ';
        $html .= 'class="form-input w-full px-3 py-2 pr-10 border-2 border-gray-200 rounded-lg bg-white text-gray-900 text-xs transition-all duration-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:shadow-lg hover:border-gray-300 appearance-none cursor-pointer focus:outline-none shadow-sm" ';
        if ($field->is_required) {
            $html .= 'required aria-required="true" ';
        }
        $html .= $this->buildValidationAttributes($field);
        $html .= $this->buildCustomAttributes($field);
        $html .= '>';

        $html .= '<option value="" disabled selected>-- Select ' . htmlspecialchars($field->field_label) . ' --</option>';

        if ($field->hasOptions()) {
            foreach ($field->getOptions() as $value => $label) {
                $html .= '<option value="' . htmlspecialchars($value) . '">';
                $html .= htmlspecialchars($label);
                $html .= '</option>';
            }
        }

        $html .= '</select>';
        // Dropdown arrow icon - improved
        $html .= '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 z-10">';
        $html .= '<i class="bx bx-chevron-down text-gray-400 dark:text-gray-500 text-xl"></i>';
        $html .= '</div>';
        $html .= '</div>';

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render radio buttons field
     */
    private function renderRadio($field): string
    {
        $html = '<div class="form-group">';
        $html .= '<label class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        // Determine if radio buttons should be inline (for 3 or fewer options like A/D/R)
        $optionCount = $field->hasOptions() ? count($field->getOptions()) : 0;
        $isInline = $optionCount <= 3;

        // Use flex layout for inline, vertical stack for more options
        $containerClass = $isInline ? 'flex flex-wrap gap-3' : 'space-y-2.5';
        $html .= '<div class="' . $containerClass . '">';

        if ($field->hasOptions()) {
            foreach ($field->getOptions() as $value => $label) {
                // Adjust styling based on inline vs stacked layout
                if ($isInline) {
                    // Inline layout - adjust width and reduce padding
                    $html .= '<div class="flex items-center px-3 py-2 rounded-lg border-2 border-gray-200 hover:border-primary-400 hover:bg-primary-50/50 transition-all duration-200 cursor-pointer group shadow-sm hover:shadow-md">';
                } else {
                    // Stacked layout - full width
                    $html .= '<div class="flex items-center p-2.5 rounded-lg border-2 border-gray-200 hover:border-primary-400 hover:bg-primary-50/50 transition-all duration-200 cursor-pointer group shadow-sm hover:shadow-md">';
                }
                $html .= '<input type="radio" ';
                $html .= 'id="' . $field->field_name . '_' . $value . '" ';
                $html .= 'name="' . $field->field_name . '" ';
                $html .= 'value="' . htmlspecialchars($value) . '" ';
                $html .= 'class="h-3.5 w-3.5 text-primary-600 focus:ring-2 focus:ring-primary-500 border-gray-300 cursor-pointer transition-all" ';
                if ($field->is_required) {
                    $html .= 'required aria-required="true" ';
                }
                $html .= '/>';
                $html .= '<label for="' . $field->field_name . '_' . $value . '" class="ml-2.5 text-xs font-medium text-gray-700 cursor-pointer flex-1 group-hover:text-gray-900">';
                $html .= htmlspecialchars($label);
                $html .= '</label>';
                $html .= '</div>';
            }
        }

        $html .= '</div>';

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render checkbox field
     */
    private function renderCheckbox($field, $isConditional = false): string
    {
        $html = '<div class="form-group">';
        if (!$field->hasOptions()) {
            // Check if this checkbox is conditional (has conditional logic) - check both passed flag and field directly
            $isCheckboxConditional = ($field->is_conditional && !empty($field->conditional_logic)) || $isConditional;

            // Get description position
            $descriptionPosition = $this->getDescriptionPosition($field);

            // Render description above checkbox if position is 'top' (only description, not help text)
            if ($descriptionPosition === 'top') {
                $html .= $this->renderFieldDescription($field, 'top');
            }

            // Checkbox and label on same row, help text and description on separate row below
            $html .= '<div class="flex items-start">';
            $html .= '<input type="checkbox" ';
            $html .= 'id="' . $field->field_name . '" ';
            $html .= 'name="' . $field->field_name . '" ';
            $html .= 'value="1" ';
            $html .= 'class="mt-0.5 h-5 w-5 text-primary-600 focus:ring-2 focus:ring-primary-500 border-gray-300 rounded cursor-pointer flex-shrink-0" ';
            if ($field->is_required) {
                $html .= 'required aria-required="true" ';
            }
            $html .= '/>';
            $html .= '<div class="ml-2 mt-1 flex-1">';

            // Label on first row
            $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 leading-snug cursor-pointer">';
            $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
            if ($field->is_required) {
                $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
            }
            $html .= '</label>';

            // Always render help text below checkbox (regardless of description position)
            $html .= $this->renderFieldHelpText($field, 'bottom');

            // Render description below checkbox if position is 'bottom'
            if ($descriptionPosition === 'bottom') {
                $html .= $this->renderFieldDescription($field, 'bottom');
            }

            $html .= '</div>'; // close label/description wrapper div
            $html .= '</div>'; // close flex container
        } else {
            $html .= '<label class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
            $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
            if ($field->is_required) {
                $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
            }
            $html .= '</label>';

            $html .= '<div class="space-y-2.5">';
            foreach ($field->getOptions() as $value => $label) {
                $html .= '<div class="flex items-center p-2.5 rounded-lg border-2 border-gray-200 hover:border-primary-400 hover:bg-primary-50/50 transition-all duration-200 cursor-pointer group shadow-sm hover:shadow-md">';
                $html .= '<input type="checkbox" ';
                $html .= 'id="' . $field->field_name . '_' . $value . '" ';
                $html .= 'name="' . $field->field_name . '[]" ';
                $html .= 'value="' . htmlspecialchars($value) . '" ';
                $html .= 'class="h-3.5 w-3.5 text-primary-600 focus:ring-2 focus:ring-primary-500 border-gray-300 rounded cursor-pointer transition-all" ';
                if ($field->is_required) {
                    $html .= 'required aria-required="true" ';
                }
                $html .= '/>';
                $html .= '<label for="' . $field->field_name . '_' . $value . '" class="ml-2.5 text-xs font-medium text-gray-700 cursor-pointer flex-1 group-hover:text-gray-900">';
                $html .= htmlspecialchars($label);
                $html .= '</label>';
                $html .= '</div>';
            }
            $html .= '</div>';

            // Render help text (subtle) first, then description (prominent) below it for multiple checkboxes
            $html .= $this->renderFieldHelpText($field);
            $html .= $this->renderFieldDescription($field);
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render date picker field
     */
    private function renderDate($field, $isConditional = false): string
    {
        // Padding is applied at form-field container level for conditional fields
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        $html .= '<input type="date" ';
        $html .= 'id="' . $field->field_name . '" ';
        $html .= 'name="' . $field->field_name . '" ';
        $html .= 'class="form-input w-full px-3 py-2 border-2 border-gray-200 rounded-lg bg-white text-gray-900 text-xs transition-all duration-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:shadow-lg hover:border-gray-300 focus:outline-none shadow-sm" ';
        if ($field->is_required) {
            $html .= 'required aria-required="true" ';
        }
        $html .= $this->buildValidationAttributes($field);
        $html .= $this->buildCustomAttributes($field);
        $html .= '/>';

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render file upload field
     */
    private function renderFile($field): string
    {
        $accept = $field->field_settings['accept'] ?? '*/*';
        $maxSize = $field->field_settings['max_size'] ?? null;

        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            // Help text with file size info if present (for top position)
            if ($field->field_help_text) {
                $html .= '<p class="mb-2 text-[10px] text-gray-400 dark:text-gray-300 flex items-start">';
                $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 dark:text-gray-400 flex-shrink-0 text-[10px]"></i>';
                $html .= '<span>';
                $html .= htmlspecialchars($field->field_help_text);
                if ($maxSize) {
                    $html .= ' (Max size: ' . $this->formatFileSize($maxSize) . ')';
                }
                $html .= '</span>';
                $html .= '</p>';
            } elseif ($maxSize) {
                $html .= '<p class="mb-2 text-[10px] text-gray-400 dark:text-gray-300 flex items-start">';
                $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 dark:text-gray-400 flex-shrink-0 text-[10px]"></i>';
                $html .= '<span>Max size: ' . $this->formatFileSize($maxSize) . '</span>';
                $html .= '</p>';
            }
        }

        $html .= '<div class="relative">';
        $html .= '<input type="file" ';
        $html .= 'id="' . $field->field_name . '" ';
        $html .= 'name="' . $field->field_name . '" ';
        $html .= 'accept="' . htmlspecialchars($accept) . '" ';
        $html .= 'class="form-input w-full px-3 py-2 border-2 border-gray-200 rounded-lg bg-white text-gray-900 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-100 file:text-primary-700 hover:file:bg-primary-200 transition-all duration-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:shadow-lg hover:border-gray-300 cursor-pointer focus:outline-none shadow-sm" ';
        if ($field->is_required) {
            $html .= 'required aria-required="true" ';
        }
        if ($maxSize) {
            $html .= 'data-max-size="' . $maxSize . '" ';
        }
        $html .= $this->buildCustomAttributes($field);
        $html .= '/>';
        $html .= '</div>';

        // Render help text (subtle) first, then description (prominent) below it
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'bottom') {
            // Help text with file size info if present
            if ($field->field_help_text) {
                $html .= '<p class="mt-1.5 text-[10px] text-gray-400 dark:text-gray-300 flex items-start">';
                $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 dark:text-gray-400 flex-shrink-0 text-[10px]"></i>';
                $html .= '<span>';
                $html .= htmlspecialchars($field->field_help_text);
                if ($maxSize) {
                    $html .= ' (Max size: ' . $this->formatFileSize($maxSize) . ')';
                }
                $html .= '</span>';
                $html .= '</p>';
            } elseif ($maxSize) {
                // Show file size info even if no help text
                $html .= '<p class="mt-1.5 text-[10px] text-gray-400 dark:text-gray-300 flex items-start">';
                $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 dark:text-gray-400 flex-shrink-0 text-[10px]"></i>';
                $html .= '<span>Max size: ' . $this->formatFileSize($maxSize) . '</span>';
                $html .= '</p>';
            }

            // Render description (prominent) below help text
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render currency input field
     */
    private function renderCurrency($field, $isConditional = false): string
    {
        $currency = $field->field_settings['currency'] ?? 'MYR';
        $min = $field->field_settings['min'] ?? null;
        $max = $field->field_settings['max'] ?? null;

        // Padding is applied at form-field container level for conditional fields
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Render description above input if position is 'top'
        $descriptionPosition = $this->getDescriptionPosition($field);
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        $html .= '<div class="relative">';
        $html .= '<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">';
        $html .= '<span class="text-gray-700 text-xs font-semibold">' . htmlspecialchars($currency) . '</span>';
        $html .= '</div>';
        $html .= '<input type="number" ';
        $html .= 'id="' . $field->field_name . '" ';
        $html .= 'name="' . $field->field_name . '" ';
        $html .= 'step="0.01" ';
        if ($min !== null) {
            $html .= 'min="' . $min . '" ';
        }
        if ($max !== null) {
            $html .= 'max="' . $max . '" ';
        }
        if ($field->field_placeholder) {
            $html .= 'placeholder="' . htmlspecialchars($field->field_placeholder) . '" ';
        }
        $html .= 'class="form-input w-full pl-12 pr-3 py-2 border-2 border-gray-200 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-xs transition-all duration-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:shadow-lg hover:border-gray-300 focus:outline-none shadow-sm" ';
        if ($field->is_required) {
            $html .= 'required aria-required="true" ';
        }
        $html .= $this->buildValidationAttributes($field);
        $html .= $this->buildCustomAttributes($field);
        $html .= '/>';
        $html .= '</div>';

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render signature pad field
     */
    private function renderSignature($field, $isConditional = false): string
    {
        $paddingClass = $isConditional ? 'pl-6' : '';
        $html = '<div class="form-group ' . $paddingClass . '">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Get description position
        $descriptionPosition = $this->getDescriptionPosition($field);

        // Render description above signature pad if position is 'top'
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        // Get signature pad settings
        $settings = $field->field_settings ?? [];
        $width = $settings['width'] ?? 600;
        $height = $settings['height'] ?? 200;
        $backgroundColor = $settings['background_color'] ?? '#ffffff';
        $penColor = $settings['pen_color'] ?? '#000000';

        // Signature pad container
        $html .= '<div class="signature-pad-container border-2 border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-sm">';
        $html .= '<canvas id="signature_' . $field->field_name . '" 
                         class="signature-canvas border border-gray-300 dark:border-gray-600 rounded cursor-crosshair" 
                         style="background-color: ' . $backgroundColor . '; touch-action: none; width: 100%; max-width: ' . $width . 'px; height: ' . $height . 'px;"></canvas>';

        // Hidden input to store signature data (base64)
        $html .= '<input type="hidden" 
                         id="' . $field->field_name . '" 
                         name="' . $field->field_name . '" 
                         value=""';
        if ($field->is_required) {
            $html .= ' required aria-required="true"';
        }
        $html .= ' />';

        // Action buttons
        $html .= '<div class="flex justify-between items-center mt-3 gap-2">';
        $html .= '<button type="button" 
                          class="clear-signature-btn px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200"
                          data-field="' . $field->field_name . '">
                    <i class="bx bx-refresh mr-1.5"></i>Clear
                  </button>';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                    <i class="bx bx-info-circle mr-1"></i>
                    Please sign in the box above
                  </span>';
        $html .= '</div>';
        $html .= '</div>'; // Close signature-pad-container

        // Render help text (subtle) first, then description (prominent) below it (if position is 'bottom')
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        // Initialize signature pad script
        $html .= '<script>
            document.addEventListener("DOMContentLoaded", function() {
                const canvas = document.getElementById("signature_' . $field->field_name . '");
                const hiddenInput = document.getElementById("' . $field->field_name . '");
                const clearBtn = document.querySelector(\'[data-field="' . $field->field_name . '"]\');
                
                if (canvas && typeof SignaturePad !== "undefined") {
                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: "' . $backgroundColor . '",
                        penColor: "' . $penColor . '",
                        minWidth: 1,
                        maxWidth: 3,
                        throttle: 16,
                        minDistance: 5
                    });
                    
                    // Resize canvas to maintain aspect ratio
                    function resizeCanvas() {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                        signaturePad.clear();
                    }
                    
                    // Initial resize
                    resizeCanvas();
                    window.addEventListener("resize", resizeCanvas);
                    
                    // Save signature to hidden input
                    signaturePad.addEventListener("endStroke", function() {
                        if (!signaturePad.isEmpty()) {
                            hiddenInput.value = signaturePad.toDataURL("image/png");
                        }
                    });
                    
                    // Clear button
                    if (clearBtn) {
                        clearBtn.addEventListener("click", function() {
                            signaturePad.clear();
                            hiddenInput.value = "";
                        });
                    }
                } else if (canvas) {
                    console.warn("SignaturePad library not loaded. Please include signature_pad.js");
                }
            });
        </script>';

        $html .= '</div>';
        return $html;
    }

    /**
     * Render repeater/table field
     */
    private function renderRepeater($field, $isConditional = false): string
    {
        $paddingClass = $isConditional ? 'pl-6' : '';
        $html = '<div class="form-group ' . $paddingClass . '">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 dark:text-gray-100 mb-1.5 leading-snug">';
        $html .= '<span class="text-gray-900 dark:text-gray-100">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        // Get description position
        $descriptionPosition = $this->getDescriptionPosition($field);

        // Render description above table if position is 'top'
        if ($descriptionPosition === 'top') {
            $html .= $this->renderFieldDescription($field, 'top');
            $html .= $this->renderFieldHelpText($field, 'top');
        }

        // Get repeater settings
        $settings = $field->field_settings ?? [];
        $columns = $settings['columns'] ?? [
            ['name' => 'account_type', 'label' => 'Account Type', 'type' => 'text'],
            ['name' => 'account_no', 'label' => 'Account No.', 'type' => 'text']
        ];
        $minRows = $settings['min_rows'] ?? 1;
        $maxRows = $settings['max_rows'] ?? null;
        $addButtonText = $settings['add_button_text'] ?? 'Add Row';
        $removeButtonText = $settings['remove_button_text'] ?? 'Remove';

        // Repeater table container
        $html .= '<div class="repeater-container border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 overflow-hidden">';
        $html .= '<table class="w-full border-collapse" id="repeater_' . $field->field_name . '">';

        // Table header
        $html .= '<thead class="bg-gray-50 dark:bg-gray-700">';
        $html .= '<tr>';
        foreach ($columns as $column) {
            $html .= '<th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">';
            $html .= htmlspecialchars($column['label']);
            $html .= '</th>';
        }
        $html .= '<th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 w-24">Action</th>';
        $html .= '</tr>';
        $html .= '</thead>';

        // Table body
        $html .= '<tbody id="repeater_' . $field->field_name . '_body" class="bg-white dark:bg-gray-800">';
        // Initial row will be added by JavaScript
        $html .= '</tbody>';
        $html .= '</table>';

        // Add row button
        $html .= '<div class="p-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">';
        $html .= '<button type="button" 
                          class="add-repeater-row px-4 py-2 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors duration-200"
                          data-field="' . $field->field_name . '"
                          data-min-rows="' . $minRows . '"
                          ' . ($maxRows ? 'data-max-rows="' . $maxRows . '"' : '') . '>
                    <i class="bx bx-plus mr-1.5"></i>' . htmlspecialchars($addButtonText) . '
                  </button>';
        $html .= '</div>';

        // Hidden input to store JSON data
        $html .= '<input type="hidden" 
                         id="' . $field->field_name . '" 
                         name="' . $field->field_name . '" 
                         value="[]"';
        if ($field->is_required) {
            $html .= ' required aria-required="true"';
        }
        $html .= ' />';

        $html .= '</div>'; // Close repeater-container

        // Render help text and description below if position is 'bottom'
        if ($descriptionPosition === 'bottom') {
            $html .= $this->renderFieldHelpText($field, 'bottom');
            $html .= $this->renderFieldDescription($field, 'bottom');
        }

        // JavaScript for repeater functionality
        $columnsJson = json_encode($columns);
        $html .= '<script>
            document.addEventListener("DOMContentLoaded", function() {
                const fieldName = "' . $field->field_name . '";
                const columns = ' . $columnsJson . ';
                const minRows = ' . $minRows . ';
                const maxRows = ' . ($maxRows ?? 'null') . ';
                const tbody = document.getElementById("repeater_" + fieldName + "_body");
                const hiddenInput = document.getElementById(fieldName);
                const addButton = document.querySelector(\'[data-field="\' + fieldName + \'"]\');
                
                let rowCount = 0;
                
                // Function to create a new row
                function createRow() {
                    if (maxRows && rowCount >= maxRows) {
                        alert("Maximum " + maxRows + " rows allowed");
                        return;
                    }
                    
                    const row = document.createElement("tr");
                    row.className = "border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700";
                    row.dataset.rowIndex = rowCount;
                    
                    // Create cells for each column
                    columns.forEach(function(column, colIndex) {
                        const cell = document.createElement("td");
                        cell.className = "px-4 py-2";
                        
                        let input;
                        if (column.type === "select" && column.options) {
                            input = document.createElement("select");
                            input.className = "w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500";
                            input.name = fieldName + "[" + rowCount + "][" + column.name + "]";
                            
                            // Add options
                            if (column.placeholder) {
                                const option = document.createElement("option");
                                option.value = "";
                                option.textContent = column.placeholder;
                                input.appendChild(option);
                            }
                            
                            Object.keys(column.options).forEach(function(key) {
                                const option = document.createElement("option");
                                option.value = key;
                                option.textContent = column.options[key];
                                input.appendChild(option);
                            });
                        } else {
                            input = document.createElement("input");
                            input.type = column.type || "text";
                            input.className = "w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500";
                            input.name = fieldName + "[" + rowCount + "][" + column.name + "]";
                            if (column.placeholder) {
                                input.placeholder = column.placeholder;
                            }
                        }
                        
                        input.addEventListener("change", updateHiddenInput);
                        input.addEventListener("input", updateHiddenInput);
                        
                        cell.appendChild(input);
                        row.appendChild(cell);
                    });
                    
                    // Add remove button cell
                    const actionCell = document.createElement("td");
                    actionCell.className = "px-4 py-2";
                    const removeBtn = document.createElement("button");
                    removeBtn.type = "button";
                    removeBtn.className = "remove-repeater-row text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs font-medium";
                    removeBtn.innerHTML = \'<i class="bx bx-trash mr-1"></i>' . htmlspecialchars($removeButtonText) . '\';
                    removeBtn.onclick = function() {
                        if (rowCount <= minRows) {
                            alert("Minimum " + minRows + " row(s) required");
                            return;
                        }
                        row.remove();
                        rowCount--;
                        reindexRows();
                        updateHiddenInput();
                    };
                    actionCell.appendChild(removeBtn);
                    row.appendChild(actionCell);
                    
                    tbody.appendChild(row);
                    rowCount++;
                    updateHiddenInput();
                }
                
                // Function to reindex rows after removal
                function reindexRows() {
                    const rows = tbody.querySelectorAll("tr");
                    rows.forEach(function(row, index) {
                        row.dataset.rowIndex = index;
                        const inputs = row.querySelectorAll("input, select");
                        inputs.forEach(function(input) {
                            const nameMatch = input.name.match(/^(.+)\[(\d+)\]\[(.+)\]$/);
                            if (nameMatch) {
                                input.name = nameMatch[1] + "[" + index + "][" + nameMatch[3] + "]";
                            }
                        });
                    });
                }
                
                // Function to update hidden input with JSON data
                function updateHiddenInput() {
                    const rows = tbody.querySelectorAll("tr");
                    const data = [];
                    
                    rows.forEach(function(row) {
                        const rowData = {};
                        columns.forEach(function(column) {
                            const input = row.querySelector(\'input[name*="[\' + column.name + \']"], select[name*="[\' + column.name + \']"]\');
                            if (input) {
                                rowData[column.name] = input.value || "";
                            }
                        });
                        // Only add row if at least one field has value
                        if (Object.values(rowData).some(v => v !== "")) {
                            data.push(rowData);
                        }
                    });
                    
                    hiddenInput.value = JSON.stringify(data);
                }
                
                // Add button click handler
                if (addButton) {
                    addButton.addEventListener("click", createRow);
                }
                
                // Create initial rows
                for (let i = 0; i < minRows; i++) {
                    createRow();
                }
            });
        </script>';

        $html .= '</div>';
        return $html;
    }

    /**
     * Get description position from field settings (default: 'bottom')
     */
    private function getDescriptionPosition($field): string
    {
        $settings = $field->field_settings ?? [];
        $position = $settings['description_position'] ?? 'bottom';
        return in_array($position, ['top', 'bottom']) ? $position : 'bottom';
    }

    /**
     * Render field description (prominent, supports HTML)
     */
    private function renderFieldDescription($field, $position = 'bottom'): string
    {
        if (!$field->field_description) {
            return '';
        }

        $description = $field->field_description;

        // Check if description is actually empty (after stripping HTML tags and whitespace)
        $textContent = trim(strip_tags($description));
        if (empty($textContent)) {
            return '';
        }

        // Check if description contains HTML (from WYSIWYG editor)
        $isHtml = strip_tags($description) !== $description;

        // Check if description contains text-align: justify or justify alignment
        // Quill editor uses ql-align-justify class or inline style="text-align: justify"
        $hasJustify = stripos($description, 'text-align: justify') !== false ||
            stripos($description, 'text-align:justify') !== false ||
            stripos($description, 'ql-align-justify') !== false ||
            preg_match('/class="[^"]*ql-align-justify[^"]*"/i', $description) ||
            preg_match('/style="[^"]*text-align:\s*justify[^"]*"/i', $description);

        // Adjust margin based on position (top: margin-bottom, bottom: margin-top)
        $marginClass = $position === 'top' ? 'mb-3' : 'mt-3';

        // Render prominent description
        if ($isHtml) {
            // If justify is detected, don't use prose class as it may override text alignment
            // The justify alignment should be in the content itself (from Quill editor)
            if ($hasJustify) {
                // Remove prose to allow inline styles to work, add text-justify as fallback
                return '<div class="' . $marginClass . ' text-xs text-gray-700 dark:text-gray-200 leading-relaxed text-justify">' . $description . '</div>';
            } else {
                // Use prose classes for non-justified content
                return '<div class="' . $marginClass . ' text-xs text-gray-700 dark:text-gray-200 leading-relaxed prose prose-sm dark:prose-invert max-w-none">' . $description . '</div>';
            }
        } else {
            // Plain text - check for line breaks
            $hasLineBreaks = strpos($description, "\n") !== false || strpos($description, '<br') !== false;
            if ($hasLineBreaks) {
                return '<div class="' . $marginClass . ' text-xs text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line">' . nl2br(htmlspecialchars($description)) . '</div>';
            } else {
                return '<p class="' . $marginClass . ' text-xs text-gray-700 dark:text-gray-200 leading-relaxed">' . htmlspecialchars($description) . '</p>';
            }
        }
    }

    /**
     * Render notes field (HTML content display)
     */
    private function renderNotes($field): string
    {
        // Get HTML content from field_description or field_settings
        $settings = $field->getFieldSettings();
        $htmlContent = $field->field_description ?? $settings['html_content'] ?? '';

        if (empty($htmlContent)) {
            return '';
        }

        // Clean up HTML - preserve tabs and indentation
        // Convert tabs (\t) to non-breaking spaces for proper indentation (4 spaces per tab)
        $htmlContent = str_replace(["\t", "&nbsp;&nbsp;&nbsp;&nbsp;"], "&nbsp;&nbsp;&nbsp;&nbsp;", $htmlContent);
        // Remove whitespace between HTML tags (but preserve content whitespace)
        $htmlContent = preg_replace('/>\s+</', '><', $htmlContent);
        // Preserve intentional spacing - convert 2+ consecutive spaces to non-breaking spaces
        // This preserves indentation while cleaning up excessive whitespace between tags
        $htmlContent = preg_replace_callback('/(?<=>)([^<]+)(?=<)/', function ($matches) {
            // Within text content, preserve multiple spaces as non-breaking spaces
            $text = $matches[0];
            // Convert 2+ spaces to non-breaking spaces (preserve indentation)
            $text = preg_replace_callback('/ {2,}/', function ($spaces) {
                return str_repeat('&nbsp;', strlen($spaces[0]));
            }, $text);
            return $text;
        }, $htmlContent);
        $htmlContent = trim($htmlContent);

        // Render as a styled informational box (no label, no input, just content)
        // Using custom CSS classes for better formatting control
        $html = '<div class="border border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-600 rounded-xl shadow-sm p-5">';
        $html .= '<div class="text-sm text-gray-800 dark:text-gray-100 notes-content">';
        $html .= $htmlContent;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render field help text (subtle, with icon)
     */
    private function renderFieldHelpText($field, $position = 'bottom'): string
    {
        if (!$field->field_help_text) {
            return '';
        }

        // Adjust margin based on position (top: margin-bottom, bottom: margin-top)
        $marginClass = $position === 'top' ? 'mb-2' : 'mt-1.5';

        return '<p class="' . $marginClass . ' text-[10px] text-gray-400 dark:text-gray-300 flex items-start">' .
            '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 dark:text-gray-400 flex-shrink-0 text-[10px]"></i>' .
            '<span>' . htmlspecialchars($field->field_help_text) . '</span>' .
            '</p>';
    }

    /**
     * Build validation attributes from validation rules
     */
    private function buildValidationAttributes($field): string
    {
        $attrs = '';
        $rules = $field->getValidationRules();

        if (!empty($rules)) {
            $dataAttrs = [];

            if (isset($rules['min'])) {
                $dataAttrs[] = 'data-min="' . $rules['min'] . '"';
                if ($field->field_type === 'number' || $field->field_type === 'currency') {
                    $attrs .= ' min="' . $rules['min'] . '"';
                } else {
                    $attrs .= ' minlength="' . $rules['min'] . '"';
                }
            }

            if (isset($rules['max'])) {
                $dataAttrs[] = 'data-max="' . $rules['max'] . '"';
                if ($field->field_type === 'number' || $field->field_type === 'currency') {
                    $attrs .= ' max="' . $rules['max'] . '"';
                } else {
                    $attrs .= ' maxlength="' . $rules['max'] . '"';
                }
            }

            if (isset($rules['pattern'])) {
                $attrs .= ' pattern="' . htmlspecialchars($rules['pattern']) . '"';
            }

            if (!empty($dataAttrs)) {
                $attrs .= ' ' . implode(' ', $dataAttrs);
            }
        }

        return $attrs;
    }

    /**
     * Build conditional logic attributes
     * Supports both old format (single condition) and new format (multiple conditions)
     */
    private function buildConditionalAttributes($conditionalLogic): string
    {
        if (empty($conditionalLogic)) {
            return '';
        }

        $attrs = [];

        // New format: supports multiple conditions with AND/OR logic
        if (isset($conditionalLogic['action']) && isset($conditionalLogic['conditions']) && is_array($conditionalLogic['conditions'])) {
            $action = $conditionalLogic['action']; // 'show_if' or 'hide_if'
            $logic = $conditionalLogic['logic'] ?? 'and'; // 'and' or 'or'
            $conditions = $conditionalLogic['conditions'];

            // Store as JSON in data attribute for JavaScript to parse
            $dataAttr = $action === 'show_if' ? 'data-show-if-conditions' : 'data-hide-if-conditions';
            $attrs[] = $dataAttr . '="' . htmlspecialchars(json_encode([
                'logic' => $logic,
                'conditions' => $conditions
            ])) . '"';
        }
        // Old format: backward compatibility for single condition
        else if (isset($conditionalLogic['show_if'])) {
            $showIf = $conditionalLogic['show_if'];
            $attrs[] = 'data-show-if-field="' . htmlspecialchars($showIf['field'] ?? '') . '"';
            $attrs[] = 'data-show-if-operator="' . htmlspecialchars($showIf['operator'] ?? 'equals') . '"';
            $attrs[] = 'data-show-if-value="' . htmlspecialchars($showIf['value'] ?? '') . '"';
        } else if (isset($conditionalLogic['hide_if'])) {
            $hideIf = $conditionalLogic['hide_if'];
            $attrs[] = 'data-hide-if-field="' . htmlspecialchars($hideIf['field'] ?? '') . '"';
            $attrs[] = 'data-hide-if-operator="' . htmlspecialchars($hideIf['operator'] ?? 'equals') . '"';
            $attrs[] = 'data-hide-if-value="' . htmlspecialchars($hideIf['value'] ?? '') . '"';
        }

        return !empty($attrs) ? implode(' ', $attrs) : '';
    }

    /**
     * Check if a conditional field is triggered by a checkbox
     */
    private function isTriggeredByCheckbox($field, $allFields): bool
    {
        if (!$field->is_conditional || !$field->conditional_logic) {
            return false;
        }

        $conditionalLogic = $field->conditional_logic;
        $triggerFieldNames = [];

        // New format: multiple conditions
        if (isset($conditionalLogic['conditions']) && is_array($conditionalLogic['conditions'])) {
            foreach ($conditionalLogic['conditions'] as $condition) {
                if (isset($condition['field'])) {
                    $triggerFieldNames[] = $condition['field'];
                }
            }
        }
        // Old format: single condition
        else if (isset($conditionalLogic['show_if']['field'])) {
            $triggerFieldNames[] = $conditionalLogic['show_if']['field'];
        } else if (isset($conditionalLogic['hide_if']['field'])) {
            $triggerFieldNames[] = $conditionalLogic['hide_if']['field'];
        }

        // Check if any of the trigger fields is a checkbox
        foreach ($triggerFieldNames as $triggerFieldName) {
            $triggerField = $allFields->firstWhere('field_name', $triggerFieldName);
            if ($triggerField && $triggerField->field_type === 'checkbox' && !$triggerField->hasOptions()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build custom attributes
     */
    private function buildCustomAttributes($field): string
    {
        $attrs = [];
        $customAttrs = $field->getCustomAttributes();

        if (!empty($customAttrs)) {
            foreach ($customAttrs as $key => $value) {
                $attrs[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            }
        }

        if ($field->css_class) {
            $attrs[] = 'class="' . htmlspecialchars($field->css_class) . '"';
        }

        return !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
    }

    /**
     * Get section label from section name
     */
    private function getSectionLabel(string $sectionName): string
    {
        // Try to get from database first
        $section = \App\Models\FormSection::where('section_key', $sectionName)->first();
        if ($section) {
            return $section->section_label;
        }

        // Fallback to defaults if not in database
        $defaults = [
            // RAF Sections
            'applicant_info' => 'Applicant Information',
            'applicant_information' => 'Applicant Information',
            'remittance_details' => 'Remittance Details',
            'beneficiary_info' => 'Beneficiary Information',
            'beneficiary_information' => 'Beneficiary Information',
            'payment_info' => 'Payment Information',
            'payment_information' => 'Payment Information',
            'documents' => 'Supporting Documents',
            'compliance' => 'Compliance & Verification',
            // DAR Sections
            'requester_info' => 'Requester Information',
            'data_subject_info' => 'Data Subject Information',
            'request_details' => 'Request Details',
            'legal_basis' => 'Legal Basis & Compliance',
            // DCR Sections
            'correction_details' => 'Correction Details',
            // SRF Sections
            'customer_info' => 'Customer Information',
            'account_info' => 'Account Information',
            'service_details' => 'Service Details',
            'deposit_details' => 'Deposit Details',
        ];

        return $defaults[$sectionName] ?? ucfirst(str_replace('_', ' ', $sectionName));
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get validation rules for form submission
     */
    public function getValidationRules($formId, $formType = 'raf'): array
    {
        // Get form to verify it exists
        $form = Form::find($formId);
        if (!$form) {
            return [];
        }

        // Use new FormField model
        $fields = FormField::where('form_id', $formId)
            ->where('is_active', true)
            ->get();

        $rules = [];

        foreach ($fields as $field) {
            // Skip notes field type - it's informational only, not submitted
            if ($field->field_type === 'notes') {
                continue;
            }

            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            }

            $validationRules = $field->getValidationRules();

            if (!empty($validationRules)) {
                if (isset($validationRules['min'])) {
                    if (in_array($field->field_type, ['number', 'currency'])) {
                        $fieldRules[] = 'numeric|min:' . $validationRules['min'];
                    } else {
                        $fieldRules[] = 'min:' . $validationRules['min'];
                    }
                }

                if (isset($validationRules['max'])) {
                    if (in_array($field->field_type, ['number', 'currency'])) {
                        $fieldRules[] = 'max:' . $validationRules['max'];
                    } else {
                        $fieldRules[] = 'max:' . $validationRules['max'];
                    }
                }

                if (isset($validationRules['pattern'])) {
                    $fieldRules[] = 'regex:' . $validationRules['pattern'];
                }

                if ($field->field_type === 'email') {
                    $fieldRules[] = 'email';
                }

                if ($field->field_type === 'file') {
                    $fieldRules[] = 'file';
                    if (isset($validationRules['mimes'])) {
                        $fieldRules[] = 'mimes:' . implode(',', $validationRules['mimes']);
                    }
                    if (isset($validationRules['max_size'])) {
                        $fieldRules[] = 'max:' . ($validationRules['max_size'] / 1024); // Convert to KB
                    }
                }
            } else {
                // Default validation based on field type
                if ($field->field_type === 'email') {
                    $fieldRules[] = 'email';
                }
                if ($field->field_type === 'number' || $field->field_type === 'currency') {
                    $fieldRules[] = 'numeric';
                }
                if ($field->field_type === 'date') {
                    $fieldRules[] = 'date';
                }
            }

            if (!empty($fieldRules)) {
                $rules[$field->field_name] = $fieldRules;
            }
        }

        return $rules;
    }
}

