@extends('layouts.admin-minimal')

@section('title', 'View ' . $config['title'] . ' - BMMB Digital Forms')
@section('page-title', 'View ' . $config['title'] . ': ' . $form->{$config['number_field']})
@section('page-description', 'Details of ' . $config['title'])

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.forms.index', $type) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to List
    </a>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.forms.edit', [$type, $form->id]) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-colors">
            <i class='bx bx-edit mr-1.5'></i>
            Edit
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

@php
    // Helper function to display field value
    $displayValue = function($value) use ($timezoneHelper) {
        if (is_null($value) || $value === '') {
            return '<span class="text-gray-400 dark:text-gray-500 italic">N/A</span>';
        }
        if (is_bool($value)) {
            return $value ? '<span class="text-green-600 dark:text-green-400">Yes</span>' : '<span class="text-red-600 dark:text-red-400">No</span>';
        }
        if (is_array($value)) {
            return '<span class="text-xs">' . implode(', ', array_filter($value)) . '</span>';
        }
        if ($value instanceof \Carbon\Carbon) {
            return $timezoneHelper->convert($value)?->format('M d, Y h:i A');
        }
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            try {
                $date = \Carbon\Carbon::parse($value);
                return $timezoneHelper->convert($date)?->format('M d, Y');
            } catch (\Exception $e) {
                return htmlspecialchars($value);
            }
        }
        return htmlspecialchars($value);
    };

    // Helper function to format field label
    $formatLabel = function($key) {
        return ucwords(str_replace('_', ' ', $key));
    };

    // Helper function to render field section
    $renderSection = function($title, $fields, $icon = 'bx-info-circle') use ($form, $displayValue, $formatLabel) {
        $hasData = false;
        foreach ($fields as $field) {
            if ($form->$field !== null && $form->$field !== '') {
                $hasData = true;
                break;
            }
        }
        
        if (!$hasData) {
            return '';
        }
        
        $html = '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">';
        $html .= '<h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">';
        $html .= '<i class=\'bx ' . $icon . ' mr-2 text-primary-600 dark:text-primary-400\'></i>';
        $html .= $title;
        $html .= '</h3>';
        $html .= '<dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        
        foreach ($fields as $field) {
            if ($form->$field !== null && $form->$field !== '') {
                $html .= '<div>';
                $html .= '<dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">';
                $html .= $formatLabel($field);
                $html .= '</dt>';
                $html .= '<dd class="text-sm text-gray-900 dark:text-white">';
                $html .= $displayValue($form->$field);
                $html .= '</dd>';
                $html .= '</div>';
            }
        }
        
        $html .= '</dl>';
        $html .= '</div>';
        
        return $html;
    };
@endphp

<div class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
            Basic Information
        </h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    {{ $config['number_prefix'] }} Number
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white font-semibold">
                    {{ $form->{$config['number_field']} }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Status
                </dt>
                <dd>
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            'under_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'in_progress' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                            'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            'expired' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                            'partially_approved' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                        ];
                        $statusColor = $statusColors[$form->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                        {{ ucfirst(str_replace('_', ' ', $form->status)) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    User
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                    {{ $form->user ? $form->user->first_name . ' ' . $form->user->last_name : 'N/A' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Version
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                    {{ $form->version ?? 'N/A' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Created At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                    {{ $timezoneHelper->convert($form->created_at)?->format('M d, Y h:i A') }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Updated At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                    {{ $timezoneHelper->convert($form->updated_at)?->format('M d, Y h:i A') }}
                </dd>
            </div>
        </dl>
    </div>

    @if($type === 'raf')
        {!! $renderSection('Applicant Information', [
            'applicant_name', 'applicant_phone', 'applicant_email', 'applicant_address', 'applicant_city',
            'applicant_state', 'applicant_postal_code', 'applicant_country', 'applicant_id_type',
            'applicant_id_number', 'applicant_id_expiry_date'
        ], 'bx-user') !!}

        {!! $renderSection('Remittance Details', [
            'remittance_amount', 'remittance_currency', 'remittance_purpose', 'remittance_purpose_description',
            'remittance_frequency'
        ], 'bx-money') !!}

        {!! $renderSection('Beneficiary Information', [
            'beneficiary_name', 'beneficiary_relationship', 'beneficiary_address', 'beneficiary_city',
            'beneficiary_state', 'beneficiary_postal_code', 'beneficiary_country', 'beneficiary_phone',
            'beneficiary_email', 'beneficiary_bank_name', 'beneficiary_bank_account', 'beneficiary_bank_routing',
            'beneficiary_bank_swift'
        ], 'bx-user-check') !!}

        {!! $renderSection('Payment Information', [
            'payment_method', 'payment_source', 'payment_currency', 'exchange_rate', 'service_fee', 'total_amount'
        ], 'bx-credit-card') !!}

        {!! $renderSection('Supporting Documents', [
            'supporting_documents', 'id_document_path', 'proof_of_income_path', 'beneficiary_id_path',
            'bank_statement_path', 'purpose_document_path'
        ], 'bx-file') !!}

        {!! $renderSection('Compliance and Verification', [
            'aml_verified', 'kyc_verified', 'sanctions_checked', 'compliance_notes', 'risk_level'
        ], 'bx-shield-alt-2') !!}

        {!! $renderSection('Processing Information', [
            'processed_by', 'submitted_at', 'reviewed_at', 'approved_at', 'completed_at',
            'rejection_reason', 'internal_notes'
        ], 'bx-time-five') !!}

        {!! $renderSection('Tracking and Audit', [
            'ip_address', 'user_agent'
        ], 'bx-tracking') !!}

    @elseif($type === 'dar')
        {!! $renderSection('Requester Information', [
            'requester_name', 'requester_phone', 'requester_email', 'requester_address', 'requester_city',
            'requester_state', 'requester_postal_code', 'requester_country', 'requester_id_type',
            'requester_id_number', 'requester_id_expiry_date', 'requester_organization', 'requester_position',
            'requester_organization_type'
        ], 'bx-user') !!}

        {!! $renderSection('Data Subject Information', [
            'data_subject_name', 'data_subject_phone', 'data_subject_email', 'data_subject_address',
            'data_subject_city', 'data_subject_state', 'data_subject_postal_code', 'data_subject_country',
            'data_subject_id_type', 'data_subject_id_number', 'data_subject_id_expiry_date',
            'relationship_to_data_subject', 'data_subject_authorization_document_path'
        ], 'bx-user-circle') !!}

        {!! $renderSection('Data Access Request Details', [
            'request_type', 'request_description', 'data_categories', 'data_sources', 'data_period_from',
            'data_period_to', 'specific_data_items', 'urgency_level', 'justification'
        ], 'bx-file-blank') !!}

        {!! $renderSection('Legal Basis and Compliance', [
            'legal_basis', 'legal_basis_description', 'consent_obtained', 'consent_date', 'consent_method',
            'gdpr_applicable', 'ccpa_applicable', 'other_privacy_law_applicable', 'applicable_privacy_laws'
        ], 'bx-gavel') !!}

        {!! $renderSection('Data Processing Information', [
            'data_controllers', 'data_processors', 'data_retention_periods', 'data_security_measures',
            'data_transferred_third_countries', 'third_countries_list', 'safeguards_description'
        ], 'bx-data') !!}

        {!! $renderSection('Supporting Documents', [
            'supporting_documents', 'identity_document_path', 'authorization_document_path',
            'proof_of_relationship_path', 'legal_basis_document_path', 'consent_document_path',
            'other_documents_path'
        ], 'bx-file') !!}

        {!! $renderSection('Processing and Response', [
            'assigned_to', 'reviewed_by', 'submitted_at', 'acknowledged_at', 'reviewed_at',
            'responded_at', 'completed_at', 'response_deadline', 'response_summary',
            'data_provided_summary', 'rejection_reason', 'internal_notes'
        ], 'bx-time-five') !!}

        {!! $renderSection('Compliance and Verification', [
            'identity_verified', 'authorization_verified', 'legal_basis_verified', 'data_existence_confirmed',
            'verification_notes', 'risk_level', 'compliance_notes'
        ], 'bx-shield-alt-2') !!}

        {!! $renderSection('Tracking and Audit', [
            'ip_address', 'user_agent'
        ], 'bx-tracking') !!}

    @elseif($type === 'dcr')
        {!! $renderSection('Requester Information', [
            'requester_name', 'requester_phone', 'requester_email', 'requester_address', 'requester_city',
            'requester_state', 'requester_postal_code', 'requester_country', 'requester_id_type',
            'requester_id_number', 'requester_id_expiry_date', 'requester_organization', 'requester_position',
            'requester_organization_type'
        ], 'bx-user') !!}

        {!! $renderSection('Data Subject Information', [
            'data_subject_name', 'data_subject_phone', 'data_subject_email', 'data_subject_address',
            'data_subject_city', 'data_subject_state', 'data_subject_postal_code', 'data_subject_country',
            'data_subject_id_type', 'data_subject_id_number', 'data_subject_id_expiry_date',
            'relationship_to_data_subject', 'data_subject_authorization_document_path'
        ], 'bx-user-circle') !!}

        {!! $renderSection('Data Correction Request Details', [
            'correction_type', 'correction_description', 'incorrect_data_items', 'corrected_data_items',
            'data_sources', 'data_period_from', 'data_period_to', 'reason_for_correction',
            'urgency_level', 'impact_description'
        ], 'bx-edit') !!}

        {!! $renderSection('Legal Basis and Compliance', [
            'legal_basis', 'legal_basis_description', 'consent_obtained', 'consent_date', 'consent_method',
            'gdpr_applicable', 'ccpa_applicable', 'other_privacy_law_applicable', 'applicable_privacy_laws'
        ], 'bx-gavel') !!}

        {!! $renderSection('Data Accuracy and Verification', [
            'verification_documents', 'verification_method', 'third_party_verification_required',
            'third_party_verification_details', 'data_source_verification_required',
            'data_source_verification_details', 'verification_status', 'verification_notes'
        ], 'bx-check-circle') !!}

        {!! $renderSection('Correction Implementation', [
            'affected_systems', 'correction_actions', 'implementation_plan', 'target_correction_date',
            'notify_third_parties', 'third_parties_to_notify', 'notification_method'
        ], 'bx-cog') !!}

        {!! $renderSection('Supporting Documents', [
            'supporting_documents', 'identity_document_path', 'proof_of_correct_data_path',
            'authorization_document_path', 'verification_document_path', 'legal_basis_document_path',
            'other_documents_path'
        ], 'bx-file') !!}

        {!! $renderSection('Processing and Response', [
            'assigned_to', 'reviewed_by', 'submitted_at', 'acknowledged_at', 'reviewed_at',
            'correction_started_at', 'correction_completed_at', 'responded_at', 'completed_at',
            'response_deadline', 'response_summary', 'correction_summary', 'rejection_reason',
            'internal_notes'
        ], 'bx-time-five') !!}

        {!! $renderSection('Compliance and Verification', [
            'identity_verified', 'authorization_verified', 'legal_basis_verified', 'data_accuracy_verified',
            'correction_feasible', 'feasibility_notes', 'risk_level', 'compliance_notes'
        ], 'bx-shield-alt-2') !!}

        {!! $renderSection('Tracking and Audit', [
            'ip_address', 'user_agent'
        ], 'bx-tracking') !!}

    @elseif($type === 'srf')
        {!! $renderSection('Customer Information', [
            'customer_name', 'customer_phone', 'customer_email', 'customer_address', 'customer_city',
            'customer_state', 'customer_postal_code', 'customer_country', 'customer_id_type',
            'customer_id_number', 'customer_id_expiry_date', 'customer_dob', 'customer_gender',
            'customer_nationality', 'customer_occupation', 'customer_employer', 'customer_employer_address',
            'customer_annual_income', 'customer_marital_status'
        ], 'bx-user') !!}

        {!! $renderSection('Account Information', [
            'account_number', 'account_type', 'account_currency', 'account_balance',
            'account_opening_date', 'account_status', 'account_notes'
        ], 'bx-wallet') !!}

        {!! $renderSection('Service Request Details', [
            'service_type', 'service_description', 'service_category', 'service_subcategories',
            'service_amount', 'service_currency', 'urgency_level', 'preferred_completion_date',
            'special_instructions', 'reason_for_request'
        ], 'bx-cog') !!}

        {!! $renderSection('Deposit Specific Fields', [
            'deposit_type', 'deposit_method', 'deposit_source', 'deposit_source_details',
            'check_number', 'check_bank', 'check_account', 'check_date', 'wire_reference',
            'wire_originator', 'wire_beneficiary', 'deposit_notes'
        ], 'bx-money') !!}

        {!! $renderSection('Financial Information', [
            'transaction_amount', 'transaction_currency', 'exchange_rate', 'fees', 'total_amount',
            'payment_method', 'payment_details'
        ], 'bx-credit-card') !!}

        {!! $renderSection('Compliance and Risk Assessment', [
            'aml_verified', 'kyc_verified', 'sanctions_checked', 'risk_level',
            'risk_assessment_notes', 'compliance_notes', 'requires_approval', 'approval_reason'
        ], 'bx-shield-alt-2') !!}

        {!! $renderSection('Supporting Documents', [
            'supporting_documents', 'identity_document_path', 'proof_of_address_path',
            'proof_of_income_path', 'bank_statement_path', 'deposit_slip_path', 'check_image_path',
            'wire_confirmation_path', 'other_documents_path'
        ], 'bx-file') !!}

        {!! $renderSection('Processing Information', [
            'assigned_to', 'reviewed_by', 'approved_by', 'submitted_at', 'acknowledged_at',
            'reviewed_at', 'approved_at', 'started_at', 'completed_at', 'cancelled_at',
            'completion_notes', 'rejection_reason', 'cancellation_reason', 'internal_notes'
        ], 'bx-time-five') !!}

        {!! $renderSection('Service Delivery', [
            'delivery_method', 'delivery_instructions', 'delivery_address', 'delivery_contact',
            'delivery_phone', 'delivery_date', 'delivery_time', 'delivery_confirmed', 'delivery_confirmed_at'
        ], 'bx-truck') !!}

        {!! $renderSection('Tracking and Audit', [
            'ip_address', 'user_agent'
        ], 'bx-tracking') !!}
    @endif
</div>

@push('scripts')
<script>
function deleteForm(formId) {
    if (confirm('Are you sure you want to delete this form? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.forms.destroy', [$type, ':id']) }}`.replace(':id', formId);
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
