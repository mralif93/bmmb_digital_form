<?php

namespace App\Services;

use App\Models\RafFormField;
use App\Models\DarFormField;
use App\Models\DcrFormField;
use App\Models\SrfFormField;

class FormRendererService
{
    private $fieldModelMap = [
        'raf' => RafFormField::class,
        'dar' => DarFormField::class,
        'dcr' => DcrFormField::class,
        'srf' => SrfFormField::class,
    ];

    /**
     * Render a complete form dynamically from database fields
     */
    public function renderForm($formId, $formType = 'raf'): string
    {
        if (!isset($this->fieldModelMap[$formType])) {
            throw new \InvalidArgumentException("Invalid form type: {$formType}");
        }

        $fieldModel = $this->fieldModelMap[$formType];
        
        // Get all active fields for this form, grouped by section
        $fields = $fieldModel::where($formType . '_form_id', $formId)
            ->where('is_active', true)
            ->ordered()
            ->get();

        if ($fields->isEmpty()) {
            return '<p class="text-gray-500">No form fields configured. Please configure fields in admin panel.</p>';
        }

        // Ensure sections are initialized
        \App\Models\FormSection::initializeDefaults($formType);

        // Group fields by section
        $sections = $fields->groupBy('field_section');
        
        // Get ALL section order from database (not just active) for proper sorting
        $dbSections = \App\Models\FormSection::forFormType($formType)
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
        if (!isset($this->fieldModelMap[$formType])) {
            return [];
        }

        // Ensure sections are initialized
        \App\Models\FormSection::initializeDefaults($formType);

        $fieldModel = $this->fieldModelMap[$formType];
        
        $fields = $fieldModel::where($formType . '_form_id', $formId)
            ->where('is_active', true)
            ->ordered()
            ->get();

        if ($fields->isEmpty()) {
            return [];
        }

        $sections = $fields->groupBy('field_section');
        
        // Get ALL section order from database (not just active) for proper sorting
        $dbSections = \App\Models\FormSection::forFormType($formType)
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
            $sectionsData[] = [
                'name' => $sectionData['name'],
                'label' => $this->getSectionLabel($sectionData['name']),
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
        $sectionLabel = $this->getSectionLabel($sectionName);
        
        $html = '<div class="form-step" data-section="' . htmlspecialchars($sectionName) . '" data-step="' . $stepIndex . '" x-show="currentStep === ' . $stepIndex . '" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform -translate-x-4">';
        // Section content - 2 column grid layout with better spacing
        // Render fields in order, respecting grid_column setting
        $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">';

        foreach ($fields as $field) {
            $html .= $this->renderField($field);
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render a single field based on its type
     */
    public function renderField($field, $columnPosition = null): string
    {
        $conditionalData = '';
        if ($field->is_conditional && $field->conditional_logic) {
            $conditionalData = $this->buildConditionalAttributes($field->conditional_logic);
        }

        // Determine column span based on grid_column
        $gridColumn = $field->grid_column ?? 'left';
        
        // Override for full-width types (unless explicitly set to full)
        if ($gridColumn !== 'full') {
            $fullWidthTypes = ['textarea', 'radio', 'checkbox', 'file'];
            if (in_array($field->field_type, $fullWidthTypes)) {
                $gridColumn = 'full';
            }
            if (in_array($field->field_type, ['radio', 'checkbox']) && method_exists($field, 'hasOptions') && $field->hasOptions()) {
                $gridColumn = 'full';
            }
        }
        
        // Apply column span
        $colSpanClass = $gridColumn === 'full' ? 'md:col-span-2' : '';
        
        $html = '<div class="form-field ' . $colSpanClass . '" data-field-name="' . $field->field_name . '" data-grid-column="' . $gridColumn . '" ' . $conditionalData . '>';
        
        switch ($field->field_type) {
            case 'text':
            case 'email':
            case 'phone':
            case 'number':
                $html .= $this->renderTextInput($field);
                break;
            case 'textarea':
                $html .= $this->renderTextarea($field);
                break;
            case 'select':
                $html .= $this->renderSelect($field);
                break;
            case 'radio':
                $html .= $this->renderRadio($field);
                break;
            case 'checkbox':
                $html .= $this->renderCheckbox($field);
                break;
            case 'date':
                $html .= $this->renderDate($field);
                break;
            case 'file':
                $html .= $this->renderFile($field);
                break;
            case 'currency':
                $html .= $this->renderCurrency($field);
                break;
            default:
                $html .= $this->renderTextInput($field);
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render text input field
     */
    private function renderTextInput($field): string
    {
        $inputType = in_array($field->field_type, ['email', 'phone', 'number']) 
            ? $field->field_type 
            : 'text';

        $html = '<div class="form-group">';
        // Enhanced label with better visual hierarchy
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 mb-2 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

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

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description ?: $field->field_help_text) . '</span>';
            $html .= '</p>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render textarea field
     */
    private function renderTextarea($field): string
    {
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 mb-2 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

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

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description ?: $field->field_help_text) . '</span>';
            $html .= '</p>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render select dropdown field
     */
    private function renderSelect($field): string
    {
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 mb-2 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

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
        $html .= '<i class="bx bx-chevron-down text-gray-400 text-xl"></i>';
        $html .= '</div>';
        $html .= '</div>';

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description ?: $field->field_help_text) . '</span>';
            $html .= '</p>';
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
        $html .= '<label class="block text-xs font-semibold text-gray-800 mb-2.5 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

        $html .= '<div class="space-y-2.5">';

        if ($field->hasOptions()) {
            foreach ($field->getOptions() as $value => $label) {
                $html .= '<div class="flex items-center p-2.5 rounded-lg border-2 border-gray-200 hover:border-primary-400 hover:bg-primary-50/50 transition-all duration-200 cursor-pointer group shadow-sm hover:shadow-md">';
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

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description ?: $field->field_help_text) . '</span>';
            $html .= '</p>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render checkbox field
     */
    private function renderCheckbox($field): string
    {
        $html = '<div class="form-group">';
        if (!$field->hasOptions()) {
            // Single checkbox - label comes after
            $html .= '<div class="flex items-start space-x-3">';
            $html .= '<input type="checkbox" ';
            $html .= 'id="' . $field->field_name . '" ';
            $html .= 'name="' . $field->field_name . '" ';
            $html .= 'value="1" ';
            $html .= 'class="mt-1 h-5 w-5 text-primary-600 focus:ring-2 focus:ring-primary-500 border-gray-300 rounded cursor-pointer" ';
            if ($field->is_required) {
                $html .= 'required aria-required="true" ';
            }
            $html .= '/>';
            $html .= '<label for="' . $field->field_name . '" class="text-xs font-semibold text-gray-800 cursor-pointer leading-snug flex-1">';
            $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
            if ($field->is_required) {
                $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
            }
            $html .= '</label>';
            $html .= '</div>';
        } else {
            $html .= '<label class="block text-xs font-semibold text-gray-800 mb-2.5 leading-snug">';
            $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
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
        }

        // Standard help text styling with icon
        if ($field->field_description && $field->hasOptions()) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description) . '</span>';
            $html .= '</p>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render date picker field
     */
    private function renderDate($field): string
    {
        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 mb-2 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

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

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description ?: $field->field_help_text) . '</span>';
            $html .= '</p>';
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
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 mb-2 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

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

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-xs text-gray-500 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-400 flex-shrink-0 text-xs"></i>';
            $html .= '<span>';
            $html .= htmlspecialchars($field->field_description ?: $field->field_help_text);
            if ($maxSize) {
                $html .= ' (Max size: ' . $this->formatFileSize($maxSize) . ')';
            }
            $html .= '</span>';
            $html .= '</p>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render currency input field
     */
    private function renderCurrency($field): string
    {
        $currency = $field->field_settings['currency'] ?? 'MYR';
        $min = $field->field_settings['min'] ?? null;
        $max = $field->field_settings['max'] ?? null;

        $html = '<div class="form-group">';
        $html .= '<label for="' . $field->field_name . '" class="block text-xs font-semibold text-gray-800 mb-2 leading-snug">';
        $html .= '<span class="text-gray-900">' . htmlspecialchars($field->field_label) . '</span>';
        if ($field->is_required) {
            $html .= ' <span class="text-red-500 font-bold ml-1.5" aria-label="required" title="Required field">*</span>';
        }
        $html .= '</label>';

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

        // Standard help text styling with icon
        if ($field->field_description || $field->field_help_text) {
            $html .= '<p class="mt-1.5 text-[10px] text-gray-400 flex items-start">';
            $html .= '<i class="bx bx-info-circle mr-1.5 mt-0.5 text-gray-300 flex-shrink-0 text-[10px]"></i>';
            $html .= '<span>' . htmlspecialchars($field->field_description ?: $field->field_help_text) . '</span>';
            $html .= '</p>';
        }

        $html .= '</div>';
        return $html;
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
     */
    private function buildConditionalAttributes($conditionalLogic): string
    {
        if (empty($conditionalLogic)) {
            return '';
        }

        $attrs = [];
        
        if (isset($conditionalLogic['show_if'])) {
            $showIf = $conditionalLogic['show_if'];
            $attrs[] = 'data-show-if-field="' . htmlspecialchars($showIf['field'] ?? '') . '"';
            $attrs[] = 'data-show-if-operator="' . htmlspecialchars($showIf['operator'] ?? 'equals') . '"';
            $attrs[] = 'data-show-if-value="' . htmlspecialchars($showIf['value'] ?? '') . '"';
        }
        
        if (isset($conditionalLogic['hide_if'])) {
            $hideIf = $conditionalLogic['hide_if'];
            $attrs[] = 'data-hide-if-field="' . htmlspecialchars($hideIf['field'] ?? '') . '"';
            $attrs[] = 'data-hide-if-operator="' . htmlspecialchars($hideIf['operator'] ?? 'equals') . '"';
            $attrs[] = 'data-hide-if-value="' . htmlspecialchars($hideIf['value'] ?? '') . '"';
        }

        return !empty($attrs) ? implode(' ', $attrs) : '';
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
        if (!isset($this->fieldModelMap[$formType])) {
            return [];
        }

        $fieldModel = $this->fieldModelMap[$formType];
        
        $fields = $fieldModel::where($formType . '_form_id', $formId)
            ->where('is_active', true)
            ->get();

        $rules = [];

        foreach ($fields as $field) {
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

