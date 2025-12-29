<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $submission->form->name }} - {{ $submission->reference_number }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7pt;
            line-height: 1.2;
            color: #000;
            padding: 15px;
        }

        /* Header Section */
        .document-header {
            background: #ea580c;
            color: white;
            padding: 6px;
            text-align: center;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 0.3px;
        }

        .document-subtitle {
            font-size: 8pt;
            font-weight: normal;
        }

        .document-meta {
            font-size: 7pt;
            margin-top: 3px;
            opacity: 0.95;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 5px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 5px;
            border: 1px solid #000;
        }

        .info-label {
            background: #f3f4f6;
            font-weight: bold;
            width: 140px;
            font-size: 6pt;
            text-transform: uppercase;
            color: #374151;
        }

        .info-value {
            font-size: 6pt;
            color: #000;
        }

        .ref-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 8pt;
            color: #ea580c;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .data-table th {
            background: #ea580c;
            color: white;
            padding: 3px 5px;
            text-align: left;
            font-size: 6pt;
            font-weight: bold;
            border: 1px solid #c2410c;
        }

        .data-table td {
            padding: 3px 5px;
            border: 1px solid #ddd;
            font-size: 6pt;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .field-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 2px;
        }

        .field-value {
            color: #000;
        }

        /* Section Headers */
        .section-header {
            background: #c2410c;
            color: white;
            padding: 3px 6px;
            font-size: 9pt;
            font-weight: bold;
            margin: 6px 0 4px 0;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        /* Data Table */
        .data-table {
            page-break-inside: auto;
            orphans: 3;
            widows: 3;
        }

        .data-table thead {
            display: table-header-group;
        }

        /* Prevent orphaned sections */
        .form-section {
            page-break-inside: avoid;
        }

        /* Grid Layout for Fields */
        .field-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .field-row {
            display: table-row;
        }

        .field-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            width: 50%;
        }

        .field-cell.full-width {
            width: 100%;
        }

        /* Footer */
        .document-footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 2px solid #ea580c;
            font-size: 6pt;
            color: #6b7280;
            text-align: center;
        }

        .footer-note {
            background: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 4px 6px;
            margin: 5px 0;
            font-size: 6pt;
            color: #1e40af;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .status-badge.submitted {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.completed {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Signature Box */
        .signature-box {
            border: 1px solid #ddd;
            padding: 4px;
            min-height: 50px;
            text-align: center;
            background: #fafafa;
        }

        .signature-box img {
            max-width: 180px;
            max-height: 60px;
        }

        /* DCR & DAR Specific Overrides for Black Lines */
        body.form-dcr .info-table td,
        body.form-dar .info-table td,
        body.form-dcr .data-table td,
        body.form-dar .data-table td,
        body.form-dcr .data-table th,
        body.form-dar .data-table th,
        body.form-dcr .field-cell,
        body.form-dar .field-cell,
        body.form-dcr .section-header,
        body.form-dar .section-header,
        body.form-dcr .signature-box,
        body.form-dar .signature-box,
        body.form-dcr .document-footer,
        body.form-dar .document-footer,
        body.form-dcr .important-notes,
        body.form-dar .important-notes,
        body.form-dcr .dcr-bordered-container,
        body.form-dar .dcr-bordered-container,
        body.form-dcr .dcr-details-table,
        body.form-dar .dcr-details-table,
        body.form-dcr .dcr-details-table th,
        body.form-dar .dcr-details-table th,
        body.form-dcr .dcr-details-table td,
        body.form-dar .dcr-details-table td,
        body.form-dcr .dcr-note-container,
        body.form-dar .dcr-note-container,
        body.form-dcr table,
        body.form-dar table,
        body.form-dcr td,
        body.form-dar td,
        body.form-dcr th,
        body.form-dar th {
            border-color: #000 !important;
        }

        /* Ensure dotted lines are black in DCR/DAR */
        body.form-dcr .dotted-line,
        body.form-dar .dotted-line {
            border-bottom-color: #000 !important;
        }
    </style>
</head>

<body class="form-{{ $submission->form->slug }}">
    <!-- Header -->
    <div class="document-header" style="position: relative; padding: 6px 10px;">
        <table style="width: 100%; border: 0;">
            <tr>
                <td style="width: 75%; vertical-align: middle; border: 0;">
                    <div class="document-title" style="text-align: left; margin-bottom: 2px; font-size: 11pt;">
                        {{ strtoupper($submission->form->name) }}
                    </div>
                    <div class="document-subtitle" style="text-align: left; font-size: 7pt;">BMMB Digital Forms -
                        Official Submission Receipt</div>
                    <div class="document-meta" style="text-align: left; font-size: 6pt; margin-top: 2px;">
                        Generated: {{ now()->format('d F Y, h:i A') }}
                    </div>
                </td>
                <td style="width: 25%; vertical-align: middle; text-align: right; border: 0;">
                    <img src="{{ public_path('assets/images/logo-bmmb-white.png') }}"
                        alt="Bank Muamalat Malaysia Berhad"
                        style="max-height: 50px; max-width: 200px; display: inline-block;">
                </td>
            </tr>
        </table>
    </div>

    <!-- Submission Info -->
    <table class="info-table">
        <tr>
            <td class="info-label">Reference Number</td>
            <td class="info-value">
                <span class="ref-number">{{ $submission->reference_number ?? 'N/A' }}</span>
            </td>
            <td class="info-label">Submission ID</td>
            <td class="info-value">#{{ $submission->id }}</td>
        </tr>
        <tr>
            <td class="info-label">Submission Date</td>
            <td class="info-value">{{ $submission->created_at->format('d M Y, h:i A') }}</td>
            <td class="info-label">Status</td>
            <td class="info-value">
                <span class="status-badge {{ $submission->status }}">
                    {{ strtoupper(str_replace('_', ' ', $submission->status)) }}
                </span>
            </td>
        </tr>
        @if($submission->branch)
            <tr>
                <td class="info-label">Branch</td>
                <td class="info-value" colspan="3">{{ $submission->branch->branch_name }}
                    ({{ $submission->branch->ti_agent_code }})</td>
            </tr>
        @endif
    </table>

    {{-- Important Notes Section (Static for DCR and DAR Forms) --}}
    @if(in_array($submission->form->slug, ['dcr', 'dar']))
        <div class="important-notes" style="margin-top: 2px; margin-bottom: 3px; border: 1px solid #000; padding: 2px; font-size: 6pt; color: #000; padding: 10px;">
            <div style="font-weight: bold; text-decoration: underline; margin-bottom: 2px; font-size: 7pt; color: #000;">Important Notes:</div>
            <ol style="margin: 0; padding-left: 15px; line-height: 1.3;">
                <li style="margin-bottom: 1px;">Please complete the Data Correction Request Form and ensure that your personal data provided herein is genuine and accurate.</li>
                <li style="margin-bottom: 1px;">Your request may not be processed if the information/document provided is incomplete.</li>
                <li style="margin-bottom: 1px;">Third Party Requestor is to be present at the branch / office to submit this form and for verification of information and documents required.</li>
                <li style="margin-bottom: 1px;">The supporting document(s) required in this form must be provided. We will respond within 21 days of receipt of the completed form with accompanying documents.</li>
                <li style="margin-bottom: 3px;">If you have any queries / need any guidance in filling-up this form, you may contact our Customer Service Department at the contact details below:

                    <div style="margin-top: 6px; margin-left: 2px;">
                        <div style="font-weight: bold; font-style: italic; margin-bottom: 2px; color: #000;">Head, Customer Service Department, Bank Muamalat Malaysia Berhad</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt; border: none;">
                            <tr>
                                <td style="width: 60px; vertical-align: top; font-weight: bold; font-style: italic; border: none; padding: 1px 0;">Address</td>
                                <td style="width: 10px; vertical-align: top; font-style: italic; border: none; padding: 1px 0; text-align: center;">:</td>
                                <td style="vertical-align: top; font-style: italic; border: none; padding: 1px 0;">19th Floor, Menara Bumiputra, Jalan Melaka, 51000 Kuala Lumpur</td>
                            </tr>
                            <tr>
                                <td style="width: 60px; vertical-align: top; font-weight: bold; font-style: italic; border: none; padding: 1px 0;">Telephone</td>
                                <td style="width: 10px; vertical-align: top; font-style: italic; border: none; padding: 1px 0; text-align: center;">:</td>
                                <td style="vertical-align: top; font-style: italic; border: none; padding: 1px 0;">1-300-88-8787 (Local), +603-26005500 (International)</td>
                            </tr>
                            <tr>
                                <td style="width: 60px; vertical-align: top; font-weight: bold; font-style: italic; border: none; padding: 1px 0;">Email</td>
                                <td style="width: 10px; vertical-align: top; font-style: italic; border: none; padding: 1px 0; text-align: center;">:</td>
                                <td style="vertical-align: top; font-style: italic; border: none; padding: 1px 0;">feedback@muamalat.com.my</td>
                            </tr>
                        </table>
                    </div>
                </li>
            </ol>
        </div>
    @endif

    {{-- Form Sections --}}
    @php
        use App\Services\FormSubmissionPresenter;
        $formattedData = FormSubmissionPresenter::formatSubmissionData($submission);
    @endphp

    @foreach($formattedData as $sectionName => $fields)
        {{-- Skip "Other Information" section as Important Notes is already displayed above --}}
        @if(strtolower($sectionName) === 'other information')
            @continue
        @endif

        {{-- Skip DAR sections that are rendered custom (Part D, E, F) --}}
        @if(
                $submission->form->slug === 'dar' && (
                    stripos($sectionName, 'personal data requested') !== false ||
                    stripos($sectionName, 'method of delivery') !== false ||
                    stripos($sectionName, 'data access request') !== false ||
                    stripos($sectionName, 'additional information') !== false ||
                    stripos($sectionName, 'declaration') !== false ||
                    stripos($sectionName, 'signature') !== false
                )
            )
                                                @continue
        @endif

        {{-- Skip Remittance Details, Declaration, and Signature for SRF --}}
        @if($submission->form->slug === 'srf' && (stripos($sectionName, 'remittance details') !== false || stripos($sectionName, 'declaration') !== false || stripos($sectionName, 'signature') !== false))
            @continue
        @endif

        <div class="form-section" style="margin-top: 3px;">
            {{-- Section Header --}}
            @if(
                    strtolower($sectionName) !== 'personal information'
                    && !(strtolower($sectionName) === 'data correction details' && $submission->form->slug === 'dcr')
                    && !(stripos($sectionName, 'declaration') !== false && $submission->form->slug === 'dcr')
                    && !(stripos($sectionName, 'declaration') !== false && $submission->form->slug === 'dar')
                    && !($submission->form->slug === 'srf' && (strtolower($sectionName) === 'account type' || strtolower($sectionName) === 'service request details' || stripos($sectionName, 'consent') !== false || stripos($sectionName, 'agreements') !== false || strtolower($sectionName) === 'customer information'))
                )
                                                    <div class="section-header" style="padding: 6px 10px; font-size: 9pt; font-weight: bold; border-bottom: none; background: {{ $submission->form->slug === 'srf' ? '#fff' : '#ea580c' }}; color: {{ $submission->form->slug === 'srf' ? '#000' : 'white' }}; border: 1px solid {{ $submission->form->slug === 'srf' ? '#000' : '#c2410c' }};">
                                                        {{ strtoupper($sectionName) }}
                                                    </div>
            @endif

            {{-- Special 3-column layout for Data Correction Details --}}
            @if(stripos($sectionName, 'correction') !== false || stripos($sectionName, 'part d') !== false)
                {{-- Left existing logic for Part D here --}}
                {{-- Custom DCR Layout for Personal Information (Parts A, B, C) --}}
            @elseif($submission->form->slug === 'srf' && strtolower($sectionName) === 'customer information')
                    @php
                        $getField = function ($fieldName) use ($fields) {
                            return collect($fields)->firstWhere('field_name', $fieldName)['value'] ?? '';
                        };
                        $name = $getField('header_1');
                        $holder = $getField('header_2');
                        $idNo = $getField('header_3');
                        $accNo = $getField('header_4');
                    @endphp

                    {{-- Merged Header and Customer Info Box --}}
                    <div style="border: 1px solid #000; border-bottom: none; padding: 5px; font-size: 6pt;">
                        {{-- Top Header --}}
                        <div style="margin-bottom: 5px; width: 100%;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                <tr>
                                    <td style="width: 60%; vertical-align: top;">
                                        <div style="margin-bottom: 2px;">The Manager</div>
                                        <div style="font-weight: bold; margin-bottom: 10px;">Bank Muamalat Malaysia Berhad</div>

                                        <div>
                                            I/We would like to perform the following service :-
                                        </div>
                                    </td>
                                    <td style="width: 40%; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                            <tr>
                                                <td style="text-align: right; padding-right: 5px; white-space: nowrap;">BRANCH/DEPARTMENT :</td>
                                                <td style="border-bottom: 1px dotted #000; width: 150px;">{{ $submission->branch->name ?? $submission->user->branch->name ?? '' }}</td>
                                            </tr>
                                             <tr>
                                                <td style="text-align: right; padding-right: 5px; padding-top: 5px; white-space: nowrap;">DATE :</td>
                                                <td style="border-bottom: 1px dotted #000; width: 150px; vertical-align: bottom;">{{ now()->format('d/m/Y') }}</td> 
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div style="border: 1px solid #000; padding: 5px; font-size: 6pt; margin-bottom: 3px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                {{-- Left Column: Customer Info --}}
                                <td style="width: 60%; vertical-align: top; padding-right: 10px;">
                                    <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                        <tr>
                                            <td style="width: 165px; padding: 3px 0; vertical-align: top;">Customer's/Company's Name : </td>
                                            <td style="border-bottom: 1px dotted #000; font-weight: bold; padding: 3px 0 3px 5px;">{{ $name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 165px; padding: 3px 0; vertical-align: top;">Account Holder : </td>
                                            <td style="border-bottom: 1px dotted #000; font-weight: bold; padding: 3px 0 3px 5px;">{{ $holder }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 165px; padding: 3px 0; vertical-align: top;">ID. No./Business Registration No. : </td>
                                            <td style="border-bottom: 1px dotted #000; font-weight: bold; padding: 3px 0 3px 5px;">{{ $idNo }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 165px; padding: 3px 0; vertical-align: top;">Account No. : </td>
                                            <td style="border-bottom: 1px dotted #000; font-weight: bold; padding: 3px 0 3px 5px;">{{ $accNo }}</td>
                                        </tr>
                                    </table>
                                </td>

                                {{-- Right Column: For Bank Use --}}
                                <td style="width: 40%; vertical-align: top;">
                                    <div style="border: 1px solid #000;">
                                        <div style="background: #e5e7eb; padding: 3px; font-weight: bold; border-bottom: 1px solid #000; font-style: italic;">
                                            For Bank Use
                                        </div>
                                        <div style="padding: 5px;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="vertical-align: top; width: 50%;">
                                                        <div style="margin-bottom: 3px;">1. Processing Department:</div>
                                                        <div style="margin-bottom: 2px;"><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>CBD</div>
                                                        <div style="margin-bottom: 2px;"><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>CCRD</div>
                                                        <div style="margin-bottom: 2px;"><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>COD</div>
                                                        <div style="margin-bottom: 2px;"><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>MCRC</div>
                                                        <div><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>Others: <span style="border-bottom:1px dotted #000; min-width: 50px; display:inline-block;"></span></div>
                                                    </td>
                                                    <td style="vertical-align: top; width: 50%;">
                                                        <div style="margin-bottom: 3px;">2. Product Type:</div>
                                                        <div style="margin-bottom: 2px;"><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>Deposit</div>
                                                        <div style="margin-bottom: 2px;"><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>RIB</div>
                                                        <div><span style="display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:3px;"></span>Investment</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                {{-- Custom SRF Layout for Part A (Account Type) --}}
            @elseif($submission->form->slug === 'srf' && (strtolower($sectionName) === 'account type' || strtolower($sectionName) === 'service request details'))
                @php
                    $getField = function ($fieldName) use ($fields) {
                        return collect($fields)->firstWhere('field_name', $fieldName)['value'] ?? '';
                    };
                    $isChecked = function ($fieldName) use ($getField) {
                        $value = $getField($fieldName);
                        if (is_array($value)) {
                            return !empty($value) && (in_array('Yes', $value) || in_array('1', $value) || in_array(true, $value, true));
                        }
                        return !empty($value) && $value !== '0' && $value !== 'false' && $value !== 'no';
                    };

                    // Fields
                    // 1. Transfer
                    $cTransfer = $isChecked('field_1');
                    $tAccount = $getField('field_1_1');
                    $tName = $getField('field_1_2');
                    $tAmount = $getField('field_1_3');

                    // 2. Cancellation
                    $cCancel = $isChecked('field_2');
                    $cChequeNo = $getField('field_2_1');
                    $cAmount = $getField('field_2_2');
                    $cReason = $getField('field_2_3');

                    // 3. Stop Payment
                    $cStop = $isChecked('field_3');
                    $sChequeNo = $getField('field_3_1');
                    $sName = $getField('field_3_2');
                    $sReason = $getField('field_3_3');

                    // 4. Statement
                    $cStmt = $isChecked('field_4');
                    $stmtMonth = $getField('field_4_1');

                    // 5. Closing
                    $cClose = $isChecked('field_5');

                    // 6. Conversion Qard
                    $cQard = $isChecked('field_6');
                @endphp

                <div style="border: 1px solid #000; font-size: 7pt; margin-bottom: 3px;">
                    <div style="background-color: #002b80; color: white; padding: 3px 5px; font-size: 7pt; font-weight: bold;">
                        A. Savings / Current / Investment Account-i Instruction
                    </div>
                    <div style="padding: 3px;">

                    {{-- 1. Transfer & 3. Stop Payment (Side by Side in Image? Image looks 2 cols) --}}
                    {{-- Actually image shows "1. Transfer" on Left, "3. Stop payment" on Right. "2. Cancel" Left, "4. Stmt"
                    Right. --}}
                    <table style="width: 100%; border-collapse: collapse; border: none; font-size: 7pt;">
                        <tr>
                            {{-- Col 1: Transfer --}}
                            <td style="width: 50%; vertical-align: top; padding-right: 10px; border-right: 1px solid #eee;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top;">
                                            <div
                                                style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                                {{ $cTransfer ? '✓' : '' }}
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <strong>1. Transfer of fund:</strong>
                                            <div style="font-style: italic; font-size: 6pt; margin-bottom: 2px;">(Note : Not
                                                Applicable for Foreign Currency Account & External Account)</div>

                                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                                <tr><td style="width: 120px; padding-right: 3px;">To account no.:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tAccount }}</td></tr>
                                                <tr><td style="width: 120px; padding-right: 3px;">Under the name/company of:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tName }}</td></tr>
                                                <tr><td style="width: 120px; padding-right: 3px;">Amount:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tAmount }}</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            {{-- Col 2: Stop Payment --}}
                            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top;">
                                            <div
                                                style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                                {{ $cStop ? '✓' : '' }}
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                        <td style="vertical-align: top;">
                                            <strong>3. Stop payment on cheque :</strong>
                                            <div style="height: 2px;"></div> {{-- Spacer --}}

                                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                                <tr><td style="width: 120px; padding-right: 3px;">Cheque no.:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $sChequeNo }}</td></tr>
                                                <tr><td style="width: 120px; padding-right: 3px;">Under the name/company of:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $sName }}</td></tr>
                                                <tr><td style="width: 120px; padding-right: 3px; vertical-align: top;">Reason:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $sReason }}</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        {{-- Row 2: Cancellation & Statement --}}
                        <tr>
                            {{-- Col 1: Cancellation --}}
                            <td
                                style="width: 50%; vertical-align: top; padding-right: 10px; border-right: 1px solid #eee; padding-top: 10px;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top;">
                                            <div
                                                style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                                {{ $cCancel ? '✓' : '' }}
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                        <td style="vertical-align: top;">
                                            <strong>2. Cancellation/Repurchase of Cashier's Order:</strong>
                                            <div style="height: 2px;"></div>

                                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                                <tr><td style="width: 120px; padding-right: 3px;">Cheque no.:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $cChequeNo }}</td></tr>
                                                <tr><td style="width: 120px; padding-right: 3px;">Amount:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $cAmount }}</td></tr>
                                                <tr><td style="width: 120px; padding-right: 3px; vertical-align: top;">Reason:</td><td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $cReason }}</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            {{-- Col 2: Statement --}}
                            <td style="width: 50%; vertical-align: top; padding-left: 10px; padding-top: 10px;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 18px; vertical-align: top;">
                                            <div
                                                style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                                {{ $cStmt ? '✓' : '' }}
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                                <tr><td colspan="2"><strong style="font-size: 6pt;">4. Bank account statement for the month of:</strong></td></tr>
                                                <tr><td colspan="2" style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $stmtMonth }}</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    {{-- 5. Closing --}}
                    <div style="margin-top: 2px; margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div
                                        style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                        {{ $cClose ? '✓' : '' }}
                                    </div>
                                </td>
                                <td>
                                    <strong>5. Closing of Savings / Current / Investment Account-i</strong>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- 6. Conversion --}}
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div
                                        style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                        {{ $cQard ? '✓' : '' }}
                                    </div>
                                </td>
                                <td>
                                    <strong>6. Conversion of Qard account to Tawarruq account</strong>
                                    {{-- Just showing the title as per image, text block usually follows but image cuts off --}}
                                    <div style="font-size: 6pt; margin-top: 1px; text-align: justify;">
                                        <strong>Customer:</strong><br>
                                        I/We hereby authorize Bank Muamalat Malaysia Berhad (BMMB) to utilize all my funds in my Qard account and hereby appoint BMMB as my/our agent under a Wakalah arrangement to do and execute all acts to purchase Shariah compliant commodities at the Purchase Price from time to time. I/We also hereby to appoint the Bank to be my/our agent restricted only to conclude the sale of the commodity and to enter into, on my/our behalf, the Sale Transaction with the Bank at the Murabahah Sale Price, pursuant to which my/our funds shall be converted into a Tawarruq account and be governed by the terms and conditions of the Tawarruq account.
                                    </div>
                                </td>
                            </tr>
                        </table>

                        {{-- If full text is needed, it can be added here, but image shows minimal text below --}}
                        {{-- Text block moved inside table --}}
                    </div>

                    {{-- 7. Mudarabah --}}
                    @php $cMud = $isChecked('field_7'); @endphp
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px;">
                                        {{ $cMud ? '✓' : '' }}
                                    </div>
                                </td>
                                <td>
                                    <strong>7. Conversion of Savings Account-i / Current Account-i (Tawarruq/Qard) to Mudarabah Unrestricted Investment Account (SURIA)</strong>
                                    <div style="font-size: 6pt; margin-top: 1px; text-align: justify;">
                                        <strong>Investment Account Holder:</strong><br>
                                        I/We hereby give a sum of money as the investment capital, once the money being placed into my/our account for the purpose of this investment under the contract of Mudarabah. I/We hereby authorise the Bank to invest the said money in any permitted investment which do not contravene with the Shariah principle. I/We hereby agree with the profit sharing ratio as per the Bank's Terms & Conditions based on Mudarabah concept and any loss incurred from the said permitted investment shall be my/our liability.
                                    </div>
                                    <div style="font-size: 6pt; margin-top: 1px; text-align: justify;">
                                        <strong>Bank:</strong><br>
                                        We, hereby agree to invest the money invested in the account under the contract of Mudarabah and hereby convenant to invest the same in the permitted investment only which does not contravene with the Shariah principle. We hereby agree with the profit sharing ratio as per the Bank's Terms & Conditions based on Mudarabah concept. We shall not be held liable for any loss incurred from the said investment save and except for any loss arising from our wilful misconduct, gross negligence or fraud.
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- 8. Zakat Savings --}}
                    @php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $cZak = $isChecked('field_8');
                        $zakSav = $isChecked('field_8_1');
                        $zakCur = $isChecked('field_8_2');
                        $zakAgent = $getField('field_8_3');
                    @endphp
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                        {{ $cZak ? '✓' : '' }}
                                    </div>
                                </td>
                                <td>
                                    <strong>8. Zakat on Savings Auto Deduction</strong>

                                    <div style="font-size: 6pt; margin-top: 1px;">
                                        Customer:<br>
                                        I/We hereby agree to authorize Bank Muamalat Malaysia Berhad (BMMB) to perform zakat on savings auto calculation and deduction on the following accounts:
                                    </div>

                                    <table style="width: 100%; border-collapse: collapse; margin-top: 2px; font-size: 6pt;">
                                        <tr>
                                            <td style="width: 15px; vertical-align: top; padding-left: 10px; padding-bottom: 5px;">a)</td>
                                            <td style="width: 15px; vertical-align: top; padding-bottom: 5px;">
                                                <div style="width: 10px; height: 10px; border: 1px solid #000; text-align: center; line-height: 8px; font-weight: bold;">
                                                    {{ $zakSav ? '✓' : '' }}
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 3px; padding-bottom: 5px;">My/our savings account; and/or</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 15px; vertical-align: top; padding-left: 10px;">b)</td>
                                            <td style="width: 15px; vertical-align: top;">
                                                <div style="width: 10px; height: 10px; border: 1px solid #000; text-align: center; line-height: 8px; font-weight: bold;">
                                                    {{ $zakCur ? '✓' : '' }}
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 3px;">My/our current account</td>
                                        </tr>
                                    </table>

                                    <div style="margin-top: 2px; font-size: 6pt;">
                                        on behalf of me/us and hereby appoint BMMB as my/our agent under a Wakalah arrangement by transferring the zakat payable to
                                        <span style="border-bottom: 1px dotted #000; font-weight: bold; display: inline-block; min-width: 120px; text-align: center;">{{ $zakAgent }}</span>
                                        (name of zakat for each state)
                                    </div>

                                    <div style="font-size: 6pt; margin-top: 1px;">
                                        Bank:<br>
                                        We, hereby agree to accept the appointment as the agent as stipulated in the offer above. We will, in performing our obligations in relation to the transactions, protect the interest of the Customer and act in good faith. Your zakat on savings payable amount shall be transferred to state zakat authority as per above subject to fulfilment of zakat on savings obligation requirements.
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Footer Notices for Page 1 --}}
                    <div style="margin-top: 10px; margin-left: -3px; margin-right: -3px; margin-bottom: -3px; border: 1px solid #000; border-top: 1px solid #000; padding: 5px; font-size: 6pt;">
                        <div style="padding-bottom: 5px; margin-left: -5px; margin-right: -5px; padding-left: 5px; padding-right: 5px; border-bottom: 1px solid #000; margin-bottom: 5px;">
                            <em>(Applicable for SURIA Investment Account-i Mudarabah)</em><br>
                            <strong>THE RETURNS ON THE SURIA INVESTMENT ACCOUNT WILL BE AFFECTED BY THE PERFORMANCE OF THE UNDERLYING ASSETS. THE PRINCIPAL AND RETURNS ARE NOT GUARANTEED AND INVESTMENT ACCOUNT HOLDER RISKS EARNING NO RETURNS AT ALL. SURIA ACCOUNT IS NOT PROTECTED BY PIDM.</strong>
                        </div>
                        <div>
                            <em>(Applicable for Savings Account-i / Current Account-i (Tawarruq/Qard))</em><br>
                            <strong>PROTECTED BY PIDM UP TO RM250,000 FOR EACH DEPOSITOR.</strong>
                        </div>
                    </div>

                    {{-- 9. Cancel Zakat Savings - PAGE BREAK HERE --}}
                    @php $cCancelZak = $isChecked('field_9'); @endphp
                    <div style="margin-top: 2px; margin-left: -3px; margin-right: -3px; padding-top: 5px; padding-left: 4px; padding-right: 4px; border-top: 1px solid #000; page-break-before: always;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                        {{ $cCancelZak ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top;">
                                    <strong>9. Cancellation or Stop Zakat on savings Auto Deduction with immediate effect</strong>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- 10. Zakat Gold --}}
                    @php 
                                                                                                                        $cZakGold = $isChecked('field_10');
                        $zGoldMYR = $isChecked('field_10_1');
                        $zGoldGram = $isChecked('field_10_2');
                        $zGoldAgent = $getField('field_10_3');
                    @endphp
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                        {{ $cZakGold ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top;">
                                    <strong>10. Zakat on Gold Account Auto Calculation and Deduction</strong>

                                    <div style="font-size: 6pt; margin-top: 1px; line-height: 1.2;">
                                        Customer:<br>
                                        I/We hereby agree to authorize Bank Muamalat Malaysia Berhad (BMMB) to perform zakat on gold account auto calculation and deduction on the following method:
                                    </div>

                                    <table style="width: 100%; border-collapse: collapse; margin-top: 2px; font-size: 6pt;">
                                        <tr>
                                            <td style="width: 15px; vertical-align: top; padding-left: 10px; padding-bottom: 5px;">a)</td>
                                            <td style="width: 15px; vertical-align: top; padding-bottom: 5px;">
                                                <div style="width: 10px; height: 10px; border: 1px solid #000; text-align: center; line-height: 8px; font-weight: bold;">
                                                    {{ $zGoldMYR ? '✓' : '' }}
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 3px; padding-bottom: 5px;">
                                                Conversion to Malaysian Ringgit (MYR)<br>
                                                - The amount of zakat payable from the gold account will be converted into MYR and will be credited to the preferred zakat state account.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 15px; vertical-align: top; padding-left: 10px;">b)</td>
                                            <td style="width: 15px; vertical-align: top;">
                                                <div style="width: 10px; height: 10px; border: 1px solid #000; text-align: center; line-height: 8px; font-weight: bold;">
                                                    {{ $zGoldGram ? '✓' : '' }}
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 3px;">
                                                Transfer in gold form (in gram)<br>
                                                - The amount of zakat payable is transferred from customer's gold account to the preferred zakat state's gold account.
                                            </td>
                                        </tr>
                                    </table>

                                    <div style="margin-top: 2px; font-size: 6pt;">
                                        on behalf of me/us and hereby appoint BMMB as my/our agent under a Wakalah arrangement by transferring the zakat payable to
                                        <span style="border-bottom: 1px dotted #000; font-weight: bold; display: inline-block; min-width: 120px; text-align: center;">{{ $zGoldAgent }}</span>
                                        (name of zakat for each state)
                                    </div>

                                    <div style="font-size: 6pt; margin-top: 1px; line-height: 1.2;">
                                        Bank:<br>
                                        We, hereby agree to accept the appointment as the agent as stipulated in the offer above. We will, in performing our obligations in relation to the transactions, protect the interest of the Customer and act in good faith. Your zakat on gold account payable amount shall be transferred to state zakat authority as per above subject to fulfilment of zakat on gold account obligation requirements.
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- 11. Cancel Zakat Gold --}}
                    @php $cCancelZakGold = $isChecked('field_11'); @endphp
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                        {{ $cCancelZakGold ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top;">
                                    <strong>11. Cancellation or Stop Zakat on gold account Auto Deduction with immediate effect.</strong>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- DISCLAIMER --}}
                    <div style="margin-top: 3px; margin-bottom: 3px; margin-left: 18px; font-size: 6pt;">
                        <strong><u>Disclaimer:</u></strong><br>
                         I/we hereby acknowledge that the Bank shall not be liable for any loss or damage that may arise due to my/our failure or delay to keep the Bank updated as to any changes to my/our information, instruction or details pertaining to my/our Accounts unless such loss or damage are attributable by the Bank's wilful misconduct, gross negligence or fraud.
                    </div>

                    {{-- 12. Physical Delivery --}}
                    @php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $cPhys = $isChecked('field_12');
                        $physRM = $getField('field_12_1');    
                    @endphp
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                            <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                        {{ $cPhys ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top;">
                                    <strong>12. Physical Delivery of Purchased Commodity:</strong>
                                    <div style="margin-top: 1px;">
                                        I hereby place the sum of RM <span style="border-bottom: 1px dotted #000; font-weight: bold; display: inline-block; min-width: 80px;">{{ $physRM }}</span> for the purpose of purchasing the commodity from the commodity supplier and opt to take physical delivery of the commodity. All costs associated with the delivery and subsequent transfer of ownership of said commodity shall be borne by me.
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- 13. Others --}}
                    @php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $cOthers = $isChecked('field_13');
                        $othersText = $getField('field_13_1');
                    @endphp
                    <div style="margin-top: 2px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                             <tr>
                                <td style="width: 18px; vertical-align: top;">
                                    <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                        {{ $cOthers ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top;">
                                    <strong>13. Others:</strong>
                                    <span style="border-bottom: 1px dotted #000; font-weight: bold; display: inline-block; min-width: 200px; margin-left: 5px;">{{ $othersText }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </div>
                </div>

            @elseif($submission->form->slug === 'srf')
                @php
                    // Helper to get field value (local section first, then global submission data)
                    $getField = function ($fieldName) use ($fields, $submission) {
                        $local = collect($fields)->firstWhere('field_name', $fieldName)['value'] ?? null;
                        if ($local !== null && $local !== '')
                            return $local;
                        return $submission->submission_data[$fieldName] ?? '';
                    };

                    // Helper to check if a checkbox is checked
                    $isChecked = function ($fieldName) use ($getField) {
                        $value = $getField($fieldName);
                        if (is_array($value)) {
                            // Check for various truthy values in array (Yes, 1, true, string 'true')
                            return !empty($value) && (
                                in_array('Yes', $value) ||
                                in_array('1', $value) ||
                                in_array(true, $value, true) ||
                                in_array('true', $value)
                            );
                        }
                        return !empty($value) && $value !== '0' && $value !== 'false' && $value !== 'no';
                    };
                    $cAgree = $isChecked('content_1');
                    $cDisagree = $isChecked('content_2');
                @endphp

                    <div style="border: 1px solid #000; font-size: 6pt; margin-bottom: 2px;">
                        {{-- Part B Header --}}
                        <div style="background-color: #002b80; color: white; padding: 3px 5px; font-size: 7pt; font-weight: bold;">B. Update PDPA Consent</div>

                        {{-- Content --}}
                        <div style="padding: 5px;">
                            <div style="margin-bottom: 2px;">
                                Updating of prior consent given by you in relation to purpose of cross selling, marketing and promotions.
                            </div>

                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                <tr>
                                    <td style="width: 25px; vertical-align: top; padding-bottom: 2px;">
                                        <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                            {{ $cAgree ? '✓' : '' }}
                                                        </div>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <div style="margin-top: 1px;">Yes, I agree to receive marketing promotions.</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25px; vertical-align: top;">
                                        <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                            {{ $cDisagree ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <div style="margin-top: 1px;">No, I do not agree to receive marketing promotions</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- PART C: Third Party Requester --}}
                    @php
                        $tpName = $getField('section_c_1') ?? '';
                        $tpNric = $getField('section_c_2') ?? '';
                        $tpRelationship = $getField('section_c_3') ?? '';
                        $tpAddress = $getField('section_c_4') ?? '';
                        $tpMobile = $getField('section_c_5') ?? '';
                        $tpEmail = $getField('section_c_6') ?? '';
                        $tpPurpose = $getField('section_c_7') ?? '';
                        $tpDeath = $isChecked('section_c_8_1');
                        $tpBirth = $isChecked('section_c_8_2');
                        $tpMarriage = $isChecked('section_c_8_3');
                        $tpOthers = $isChecked('section_c_8_4');
                        $tpOthersText = $getField('section_c_8_5') ?? '';
                    @endphp
                    <div style="border: 1px solid #000; margin-bottom: 2px;">
                        {{-- Part C Header --}}
                        <div style="background-color: #002b80; color: white; padding: 3px 5px; font-size: 7pt; font-weight: bold;">C. Third Party Requester</div>

                        {{-- Permitted disclosures notice --}}
                        <div style="background-color: #002b80; color: white; padding: 2px 5px; font-size: 6pt;">(Permitted disclosures - Item 2 Schedule 11 (subsection 146 (1)) Islamic Financial Services Act 2013</div>

                        {{-- Fields --}}
                        <div style="padding: 5px; font-size: 6pt;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 20%; padding: 2px 0; vertical-align: top;">Beneficiary Name:</td>
                                    <td style="width: 2%;">:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; vertical-align: top;">NRIC/ Passport No.:</td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpNric }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; vertical-align: top;">Relationship With Account Holder:</td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpRelationship }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; vertical-align: top;">Address:</td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpAddress }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; vertical-align: top;">Mobile No.:</td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpMobile }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; vertical-align: top;">Email Address:</td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpEmail }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; vertical-align: top;">Purpose of request:</td>
                                    <td>:</td>
                                    <td style="border-bottom: 1px dotted #000; font-weight: bold;">{{ $tpPurpose }}</td>
                                </tr>
                            </table>

                            {{-- Supporting documents --}}
                            <div style="margin-top: 5px;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 20%; padding: 2px 0; vertical-align: top;">Supporting documents:</td>
                                        <td>
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 50%; padding: 2px 0;">
                                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; vertical-align: middle;">{{ $tpDeath ? '✓' : '' }}</div>
                                                        Death certificate
                                                    </td>
                                                    <td style="width: 50%; padding: 2px 0;">
                                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; vertical-align: middle;">{{ $tpBirth ? '✓' : '' }}</div>
                                                        Birth Certificate
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 2px 0;">
                                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; vertical-align: middle;">{{ $tpMarriage ? '✓' : '' }}</div>
                                                        Marriage certificate
                                                    </td>
                                                    <td style="padding: 2px 0;">
                                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; vertical-align: middle;">{{ $tpOthers ? '✓' : '' }}</div>
                                                        Others: <span style="border-bottom: 1px dotted #000; display: inline-block; min-width: 100px;">{{ $tpOthersText }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Part D: Confirmation / Pengesahan --}}
                    <div style="border: 1px solid #000; font-size: 6pt;">
                        <div style="background-color: #002b80; color: #fff; padding: 3px 5px; font-weight: bold; font-size: 7pt;">D. Confirmation</div>
                        <div style="padding: 5px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    {{-- Left Column: Customer Declaration --}}
                                    <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                                        @php $dDeclare = $isChecked('section_d_1'); @endphp
                                        <div style="margin-bottom: 3px;">
                                            <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                                <tr>
                                                    <td style="width: 18px; vertical-align: top;">
                                                        <div style="width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-weight: bold;">
                                                            {{ $dDeclare ? '✓' : '' }}
                                                        </div>
                                                    </td>
                                                    <td style="vertical-align: top;">
                                                        I/We declare(s) that the above information is correct.
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                        {{-- Signature Box --}}
                                        <div style="border: 1px solid #000; height: 50px; margin-bottom: 2px; text-align: center; overflow: hidden;">
                                            @php
                                                $dSignature = $getField('section_d_2');
                                                // Handle file path vs base64
                                                if ($dSignature && !str_starts_with($dSignature, 'data:image')) {
                                                    $dSignature = str_replace('storage/', '', $dSignature);
                                                    $dSignature = public_path('storage/' . $dSignature);
                                                }
                                            @endphp
                                            @if($dSignature)
                                                <img src="{{ $dSignature }}" style="max-height: 48px; max-width: 100%; margin-top: 1px;" alt="Signature">
                                            @endif
                                        </div>
                                        <div style="font-style: italic; margin-bottom: 3px;">Signature</div>

                                        <div style="margin-bottom: 3px;">
                                            Date: <span style="font-weight: bold;">{{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                                        </div>

                                        <div style="font-size: 5pt; margin-top: 5px; line-height: 1.1;">* Failure to provide the Bank with required details may cause the request to be delayed/rejected.</div>
                                    </td>

                                    {{-- Right Column: Bank Use --}}
                                    <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                                        <div style="border: 1px solid #000;">
                                            <div style="background-color: #d1d5db; padding: 3px 5px; font-weight: bold; border-bottom: 1px solid #000;">For Bank Use</div>
                                            <div style="padding: 5px;">
                                                <div style="margin-bottom: 20px;">Attended by (Signature & Name):</div>
                                                <div style="margin-bottom: 2px; border-bottom: 1px dotted #000;"></div>
                                                <div style="margin-bottom: 5px;">Date:</div>

                                                <div style="margin-bottom: 35px; margin-top: 10px;">Verified by (Signature & Name):</div>
                                                <div style="margin-bottom: 2px; border-bottom: 1px dotted #000;"></div>
                                                <div>Date:</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

            @elseif($submission->form->slug === 'dcr' && strtolower($sectionName) === 'personal information')
                    @php
                        $getField = function ($fieldName) use ($fields) {
                            return collect($fields)->firstWhere('field_name', $fieldName)['value'] ?? '';
                        };

                        // Helper to check if a checkbox is checked (handles array and string values)
                        $isChecked = function ($fieldName) use ($getField) {
                            $value = $getField($fieldName);
                            if (is_array($value)) {
                                return !empty($value) && (in_array('Yes', $value) || in_array('1', $value) || in_array(true, $value, true));
                            }
                            return !empty($value) && $value !== '0' && $value !== 'false' && $value !== 'no';
                        };

                        $isCustomer = $isChecked('field_2_1');
                        $isThirdParty = $isChecked('field_2_2');

                        // Part B Data
                        $bName = $getField('field_3_1');
                        $bIC = $getField('field_3_2');
                        $bAddress = $getField('field_3_3');
                        $bPostcode = $getField('field_3_4');
                        $bEmail = $getField('field_3_5');
                        $bTel = $getField('field_3_6');
                        $bMobile = $getField('field_3_7');
                    @endphp

                    {{-- Part A: About Yourself --}}
                    <div style="border: 1px solid #000; padding: 10px; margin-bottom: 4px; font-size: 6pt;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">PART A : ABOUT YOURSELF</div>
                        <table style="width: 100%; border-collapse: collapse; border: none; font-size: 6pt;">
                            <tr>
                                <td style="width: 20px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $isCustomer ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 0; border: none;">
                                    I am a customer / former customer and I would like to correct my personal data
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $isThirdParty ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 1px 0; border: none;">
                                    I am a Third Party Requestor [i.e. I am making this personal data correction request for another
                                    person.]
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Part B: Particulars of Data Subject --}}
                    <div style="border: 1px solid #000; padding: 10px; margin-bottom: 4px; font-size: 6pt;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 6px;">PART B : PARTICULARS OF THE DATA SUBJECT (ACCOUNT HOLDER)</div>

                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                            {{-- R1: Full Name --}}
                            <tr>
                                <td colspan="3" style="padding-bottom: 5px;">
                                    <div style="margin-bottom: 2px;">Full name (as per NRIC):</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bName }}
                                    </div>
                                </td>
                            </tr>
                            {{-- R2: NRIC --}}
                            <tr>
                                <td colspan="3" style="padding-bottom: 5px;">
                                    <div style="margin-bottom: 2px;">NRIC/Passport Number:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bIC }}
                                    </div>
                                </td>
                            </tr>
                            {{-- R3: Address | Postcode --}}
                            <tr>
                                <td colspan="2" style="padding-bottom: 5px; padding-right: 15px; vertical-align: top;">
                                    <div style="margin-bottom: 2px;">Address:</div>
                                    <div
                                        style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold; line-height: 1.4;">
                                        {{ $bAddress }}
                                    </div>
                                </td>
                                <td style="width: 25%; padding-bottom: 5px; vertical-align: top;">
                                    <div style="margin-bottom: 2px;">Postcode:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">
                                        {{ $bPostcode }}
                                    </div>
                                </td>
                            </tr>
                            {{-- R4: Telephone No | Mobile --}}
                            {{-- R4: Contact Info (3 Columns) --}}
                            <tr>
                                <td style="width: 35%; padding-bottom: 5px; vertical-align: top; padding-right: 10px;">
                                    <div style="margin-bottom: 2px;">Telephone No:- Office/Home:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bTel }}
                                    </div>
                                </td>
                                <td style="width: 30%; padding-bottom: 5px; vertical-align: top; padding-right: 10px;">
                                    <div style="margin-bottom: 2px;">Mobile:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bMobile }}
                                    </div>
                                </td>
                                <td style="width: 35%; padding-bottom: 5px; vertical-align: top;">
                                    <div style="margin-bottom: 2px;">E-mail:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bEmail }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Part C: Particulars of Third Party Requestor --}}
                    <div
                        style="border: 1px solid #000; padding: 10px; margin-bottom: 4px; font-size: 6pt; page-break-inside: avoid;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">PART C : PARTICULARS OF THIRD PARTY REQUESTOR</div>
                        <div style="font-style: italic; font-size: 6pt; margin-bottom: 6px;">[ to be filled if request is made by a person other than Data Subject (account holder) ]</div>

                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                            {{-- R1: Full Name --}}
                            <tr>
                                <td colspan="3" style="padding-bottom: 4px;">
                                    <div style="margin-bottom: 1px;">Full name (as per NRIC):</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold;"></div>
                                </td>
                            </tr>
                            {{-- R2: NRIC --}}
                            <tr>
                                <td colspan="3" style="padding-bottom: 4px;">
                                    <div style="margin-bottom: 1px;">NRIC/Passport Number:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold;"></div>
                                </td>
                            </tr>
                            {{-- R3: Address | Postcode --}}
                            <tr>
                                <td colspan="2" style="padding-bottom: 4px; padding-right: 10px; vertical-align: top;">
                                    <div style="margin-bottom: 1px;">Address:</div>
                                    <div
                                        style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold; line-height: 1.3;">
                                    </div>
                                </td>
                                <td style="width: 25%; padding-bottom: 4px; vertical-align: top;">
                                    <div style="margin-bottom: 1px;">Postcode:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold;"></div>
                                </td>
                            </tr>
                            {{-- R4: Telephone No | Mobile --}}
                            {{-- R4: Contact Info (3 Columns) --}}
                            <tr>
                                <td style="width: 35%; padding-bottom: 4px; vertical-align: top; padding-right: 8px;">
                                    <div style="margin-bottom: 1px;">Telephone No:- Office/Home:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold;"></div>
                                </td>
                                <td style="width: 30%; padding-bottom: 4px; vertical-align: top; padding-right: 8px;">
                                    <div style="margin-bottom: 1px;">Mobile:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold;"></div>
                                </td>
                                <td style="width: 35%; padding-bottom: 4px; vertical-align: top;">
                                    <div style="margin-bottom: 1px;">E-mail:</div>
                                    <div style="border-bottom: 1px dotted #000; min-height: 14px; font-weight: bold;"></div>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-bottom: 2px; margin-top: 5px;">I am making this request for the correction of personal
                            data of Data Subject (account holder) because Data Subject (account holder) :</div>

                        {{-- Part C Checkboxes --}}
                        @php
                            $cMinor = $isChecked('field_3_8');
                            $cIncapable = $isChecked('field_3_9');
                            $cDeceased = $isChecked('field_3_10');
                            $cAuth = $isChecked('field_3_11');
                            $cOther = $isChecked('field_3_12');
                            $cOtherReason = $getField('field_3_12_1');
                        @endphp

                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $cMinor ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 7pt;">
                                        is a minor and I am the parent / legal guardian / parental responsibility over the Data
                                        Subject (account holder)
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $cIncapable ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 7pt;">
                                        is incapable of managing his/her affairs and I have been appointed by Court to manage his /
                                        her affairs
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $cDeceased ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 7pt;">
                                        had passed away and I have been appointed as administrator of Data Subject's (account
                                        holder) estate
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $cAuth ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                        authorised me in writing to make this data correction request
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $cOther ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                        other reason: (please specify): <span
                                            style="border-bottom: 1px dotted #000; min-width: 200px; display: inline-block; margin-left: 5px;">{{ $cOtherReason }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style="margin-top: 5px; margin-bottom: 2px;">In proof of my capacity, I enclose the
                            following:</div>

                        @php
                            $docNRIC = $isChecked('field_3_13');
                            $docCourt = $isChecked('field_3_14');
                            $docAuth = $isChecked('field_3_15');
                            $docOther = $isChecked('field_3_16');
                            $docOtherSpec = $getField('field_3_16_1');
                        @endphp

                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $docNRIC ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                        copy of my NRIC /MyKid/Birth certificate for minor account, Passport (original to be
                                        produced for inspection); and
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $docCourt ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                        original of Court Order / Power of Attorney
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $docAuth ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                        original of authorisation letter from Data Subject (account holder)
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 3px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ $docOther ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                        other reason: (please specify): <span
                                            style="border-bottom: 1px dotted #000; min-width: 200px; display: inline-block; margin-left: 5px;">{{ $docOtherSpec }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- DCR Part D: Force Render after Part C --}}
                    <div style="margin-top: 0px; page-break-before: always; page-break-inside: avoid; border: 1px solid #000; border-bottom: none; padding: 10px;">
                        @php
                            // Fetch Part D Data (Raw) to ensure it renders even if section is missing
                            $getRawD = function ($fieldName) use ($submission) {
                                $sd = $submission->submissionData->first(function ($item) use ($fieldName) {
                                    return ($item->field->field_name ?? '') === $fieldName;
                                });
                                if ($sd)
                                    return $sd->field_value;
                                return $submission->field_responses[$fieldName] ?? '';
                            };

                            $updateScope = $getRawD('field_4_1');
                            $accType1 = $getRawD('field_4_2');
                            $accNo1 = $getRawD('field_4_3');
                            $accType2 = $getRawD('field_4_4');
                            $accNo2 = $getRawD('field_4_5');
                            $accType3 = $getRawD('field_4_7');
                            $accNo3 = $getRawD('field_4_8');
                            $effectiveDate = $getRawD('field_4_6');
                        @endphp

                        <div class="dcr-bordered-container" style="background: #fff; font-size: 6pt; margin-bottom: 4px;">
                            <div style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">PART D : PERSONAL DATA CORRECTION</div>
                            <div style="margin-bottom: 2px;">Please tick [✓] the appropriate box:</div>

                            <div style="margin-bottom: 4px;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 20px; vertical-align: top; padding: 1px 0;">
                                            <div
                                                style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                                {{ strtolower($updateScope) === 'all' ? '✓' : '' }}
                                            </div>
                                        </td>
                                        <td style="vertical-align: top; padding: 3px 0 1px 0; border: none;">
                                            Please update ALL of the Data Subject’s (account holder) account(s) maintained with your branch.
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0;">
                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ strtolower($updateScope) === 'specific' ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none;">
                                        Please update ONLY the Data Subject’s (account holder) account(s) maintained with your branch as stated below:
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- Account Table --}}
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; font-size: 6pt;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #000; padding: 5px; width: 50%; text-align: center; font-weight: bold; background: #f3f4f6;">ACCOUNT TYPE</th>
                                    <th style="border: 1px solid #000; padding: 5px; width: 50%; text-align: center; font-weight: bold; background: #f3f4f6;">ACCOUNT NO.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px;">{{ $accType1 }}</td>
                                    <td style="border: 1px solid #000; padding: 5px;">{{ $accNo1 }}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px;">{{ $accType2 }}</td>
                                    <td style="border: 1px solid #000; padding: 5px;">{{ $accNo2 }}</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px;">{{ $accType3 }}</td>
                                    <td style="border: 1px solid #000; padding: 5px;">{{ $accNo3 }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div style="font-size: 6pt;">Please update the following information with effect from : <span style="display: inline-block; min-width: 100px; border-bottom: 1px dotted #000; padding: 0 5px;">{{ $effectiveDate }}</span> (DD/MM/YYYY)</div>
                    </div>

                    <table class="dcr-details-table" style="width: 100%; border-collapse: collapse; font-size: 6pt; border: 1px solid #d1d5db; border-top: none; border-color: #000 !important;">
                        <thead>
                            <tr style="background: #fff;">
                                <th rowspan="2" style="border: 1px solid #000; padding: 5px; text-align: center; width: 35%; font-weight: bold; vertical-align: middle;">PERSONAL DATA TYPE</th>
                                <th rowspan="2" style="border: 1px solid #000; padding: 5px; text-align: center; width: 50%; font-weight: bold; vertical-align: middle;">PLEASE PROVIDE THE PERSONAL DATA TO BE CORRECTED</th>
                                <th colspan="3" style="border: 1px solid #000; padding: 2px 4px; text-align: center; font-weight: bold;">Please Tick (√) the Appropriate Column</th>
                            </tr>
                            <tr style="background: #fff;">
                                <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 5%; font-weight: bold;">A</th>
                                <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 5%; font-weight: bold;">D</th>
                                <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 5%; font-weight: bold;">R</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $correctionFields = [
                                    'field_4_7' => 'Name of Data Subject (account holder)',
                                    'field_4_9' => 'Old IC No.',
                                    'field_4_11' => 'New IC No.',
                                    'field_4_13' => 'Passport No.',
                                    'field_4_15' => 'Residential/Mailing Address*',
                                    'field_4_17' => 'Postcode',
                                    'field_4_19' => 'Account Number',
                                    'field_4_21' => 'Telephone No. (House)',
                                    'field_4_23' => 'Telephone No. (Office)',
                                    'field_4_25' => 'Mobile Phone Number',
                                    'field_4_27' => 'Nationality',
                                    'field_4_29' => 'Occupation',
                                    'field_4_31' => 'Name of Employer',
                                    'field_4_33' => 'Others (Please specify)'
                                ];
                            @endphp
                            @foreach($correctionFields as $fKey => $label)
                                @php
                                    $val = $getRawD($fKey);
                                    // Action field ID is typically field + 1 (e.g. 4_7 -> 4_8)
                                    // Based on presenter mapping, actions are even numbers
                                    $parts = explode('_', $fKey);
                                    $last = intval(end($parts));
                                    $actionKey = 'field_' . $parts[1] . '_' . ($last + 1);
                                    $act = strtoupper($getRawD($actionKey));
                                @endphp
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px 3px; vertical-align: top;">
                                        <strong>{{ $label }}</strong>
                                    </td>
                                    <td style="border: 1px solid #000; padding: 5px 3px; vertical-align: top;">
                                        {{ $val }}
                                    </td>
                                    <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ ($act == 'A' || $act == 'ADD') ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ ($act == 'D' || $act == 'DELETE') ? '✓' : '' }}
                                        </div>
                                    </td>
                                    <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle;">
                                        <div
                                            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                            {{ ($act == 'R' || $act == 'REVISE') ? '✓' : '' }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="dcr-note-container"
                        style="font-size: 6pt; font-style: italic; padding: 2px 8px; border: 1px solid #000; border-top: none; background: #fff; margin-bottom: 0;">
                        <strong>Note :</strong> A; Add D; delete ; R: Revise
                    </div>
                </div>


                {{-- Custom DAR Layout for Personal Information (Parts A, B, C) - Same as DCR --}}
            @elseif($submission->form->slug === 'dar' && strtolower($sectionName) === 'personal information')
                @php
                    $getField = function ($fieldName) use ($fields) {
                        return collect($fields)->firstWhere('field_name', $fieldName)['value'] ?? '';
                    };

                    // Helper to check if a checkbox is checked (handles array and string values)
                    $isChecked = function ($fieldName) use ($getField) {
                        $value = $getField($fieldName);
                        if (is_array($value)) {
                            return !empty($value) && (in_array('Yes', $value) || in_array('1', $value) || in_array(true, $value, true));
                        }
                        return !empty($value) && $value !== '0' && $value !== 'false' && $value !== 'no';
                    };

                    $isCustomer = $isChecked('field_2_1');
                    $isThirdParty = $isChecked('field_2_2');

                    // Part B Data
                    $bName = $getField('field_3_1');
                    $bIC = $getField('field_3_2');
                    $bAddress = $getField('field_3_3');
                    $bPostcode = $getField('field_3_4');
                    $bEmail = $getField('field_3_5');
                    $bTel = $getField('field_3_6');
                    $bMobile = $getField('field_3_7');
                @endphp

                {{-- Part A: About Yourself --}}
                <div style="border: 1px solid #000; padding: 10px; margin-bottom: 3px; font-size: 6pt;">
                    <div style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">PART A : ABOUT YOURSELF</div>
                    <table style="width: 100%; border-collapse: collapse; border: none; font-size: 6pt;">
                        <tr>
                            <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                <div
                                    style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                    {{ $isCustomer ? '✓' : '' }}
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 3px 0 1px 0; border: none;">
                                I am a customer / former customer and I would like to correct my personal data
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                <div
                                    style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                    {{ $isThirdParty ? '✓' : '' }}
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 3px 0 1px 0; border: none;">
                                I am a Third Party Requestor [i.e. I am making this personal data correction request for another person.]
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Part B: Particulars of Data Subject --}}
                <div style="border: 1px solid #000; padding: 10px; margin-bottom: 3px; font-size: 6pt;">
                    <div style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">PART B : PARTICULARS OF THE DATA SUBJECT (ACCOUNT HOLDER)</div>

                    <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                        {{-- R1: Full Name --}}
                        <tr>
                            <td colspan="3" style="padding-bottom: 5px;">
                                <div style="margin-bottom: 2px;">Full name (as per NRIC):</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bName }}
                                </div>
                            </td>
                        </tr>
                        {{-- R2: NRIC --}}
                        <tr>
                            <td colspan="3" style="padding-bottom: 5px;">
                                <div style="margin-bottom: 2px;">NRIC/Passport Number:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bIC }}
                                </div>
                            </td>
                        </tr>
                        {{-- R3: Address | Postcode --}}
                        <tr>
                            <td colspan="2" style="padding-bottom: 5px; padding-right: 15px; vertical-align: top;">
                                <div style="margin-bottom: 2px;">Address:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold; line-height: 1.4;">
                                    {{ $bAddress }}
                                </div>
                            </td>
                            <td style="width: 25%; padding-bottom: 5px; vertical-align: top;">
                                <div style="margin-bottom: 2px;">Postcode:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">
                                    {{ $bPostcode }}
                                </div>
                            </td>
                        </tr>
                        {{-- R4: Telephone No | Mobile --}}
                        {{-- R4: Contact Info (3 Columns) --}}
                        <tr>
                            <td style="width: 35%; padding-bottom: 5px; vertical-align: top; padding-right: 10px;">
                                <div style="margin-bottom: 2px;">Telephone No:- Office/Home:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bTel }}
                                </div>
                            </td>
                            <td style="width: 30%; padding-bottom: 5px; vertical-align: top; padding-right: 10px;">
                                <div style="margin-bottom: 2px;">Mobile:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bMobile }}
                                </div>
                            </td>
                            <td style="width: 35%; padding-bottom: 5px; vertical-align: top;">
                                <div style="margin-bottom: 2px;">E-mail:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 16px; font-weight: bold;">{{ $bEmail }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Part C: Particulars of Third Party Requestor --}}
                <div style="border: 1px solid #000; padding: 10px; margin-bottom: 2px; font-size: 6pt; page-break-inside: avoid;">
                    <div style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">PART C : PARTICULARS OF THIRD PARTY REQUESTOR</div>
                    <div style="font-style: italic; font-size: 6pt; margin-bottom: 3px;">[ to be filled if request is made by a person other than Data Subject (account holder) ]</div>

                    <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                        {{-- R1: Full Name --}}
                        <tr>
                            <td colspan="3" style="padding-bottom: 2px;">
                                <div style="margin-bottom: 1px;">Full name (as per NRIC):</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold;"></div>
                            </td>
                        </tr>
                        {{-- R2: NRIC --}}
                        <tr>
                            <td colspan="3" style="padding-bottom: 2px;">
                                <div style="margin-bottom: 1px;">NRIC/Passport Number:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold;"></div>
                            </td>
                        </tr>
                        {{-- R3: Address | Postcode --}}
                        <tr>
                            <td colspan="2" style="padding-bottom: 2px; padding-right: 10px; vertical-align: top;">
                                <div style="margin-bottom: 1px;">Address:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold; line-height: 1.2;">
                                </div>
                            </td>
                            <td style="width: 25%; padding-bottom: 2px; vertical-align: top;">
                                <div style="margin-bottom: 1px;">Postcode:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold;"></div>
                            </td>
                        </tr>
                        {{-- R4: Telephone No | Mobile --}}
                        {{-- R4: Contact Info (3 Columns) --}}
                        <tr>
                            <td style="width: 35%; padding-bottom: 2px; vertical-align: top; padding-right: 8px;">
                                <div style="margin-bottom: 1px;">Telephone No:- Office/Home:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold;"></div>
                            </td>
                            <td style="width: 30%; padding-bottom: 2px; vertical-align: top; padding-right: 8px;">
                                <div style="margin-bottom: 1px;">Mobile:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold;"></div>
                            </td>
                            <td style="width: 35%; padding-bottom: 2px; vertical-align: top;">
                                <div style="margin-bottom: 1px;">E-mail:</div>
                                <div style="border-bottom: 1px dotted #000; min-height: 12px; font-weight: bold;"></div>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-bottom: 2px; margin-top: 5px;">I am making this request for the Access of personal data of Data Subject (account holder) because of Data Subject (account holder) :</div>

                    {{-- Part C Checkboxes --}}
                    @php
                        $cMinor = $isChecked('field_3_8');
                        $cIncapable = $isChecked('field_3_9');
                        $cDeceased = $isChecked('field_3_10');
                        $cAuth = $isChecked('field_3_11');
                        $cOther = $isChecked('field_3_12');
                        $cOtherReason = $getField('field_3_12_1');
                    @endphp

                    <div style="margin-bottom: 1px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $cMinor ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 2px 0 1px 0; border: none; font-size: 6pt;">
                                    is a minor and I am the parent / legal guardian / parental responsibility over the Data Subject (account holder)
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $cIncapable ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    is incapable of managing his/her affairs and I have been appointed by Court to manage his / her affairs
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $cDeceased ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    had passed away and I have been appointed as administrator of Data Subject's (account holder) estate
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $cAuth ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    authorised me in writing to make this data correction request
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $cOther ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    other reason: (please specify): <span
                                        style="border-bottom: 1px dotted #000; min-width: 200px; display: inline-block; margin-left: 5px;">{{ $cOtherReason }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-top: 5px; margin-bottom: 2px;">In proof of my capacity, I enclose the
                        following:</div>

                    @php
                        $docNRIC = $isChecked('field_3_13');
                        $docCourt = $isChecked('field_3_14');
                        $docAuth = $isChecked('field_3_15');
                        $docOther = $isChecked('field_3_16');
                        $docOtherSpec = $getField('field_3_16_1');
                    @endphp

                    <div style="margin-bottom: 1px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $docNRIC ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 2px 0 1px 0; border: none; font-size: 6pt;">
                                    copy of my NRIC /MyKid/Birth certificate for minor account, Passport (original to be produced for inspection); and
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $docCourt ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    original of Court Order / Power of Attorney
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 2px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $docAuth ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    original of authorisation letter from Data Subject (account holder)
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                            <tr>
                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                    <div
                                        style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                        {{ $docOther ? '✓' : '' }}
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                    other reason (please specify): <span
                                        style="border-bottom: 1px dotted #000; min-width: 200px; display: inline-block; margin-left: 5px;">{{ $docOtherSpec }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>



                {{-- Page Break before Part D --}}
                <div style="page-break-after: always;"></div>

                {{-- DAR Part D: Description of Personal Data Requested --}}
                @if($submission->form->slug === 'dar')
                    @php
                        // Helper to get raw field value for DAR Part D
                        $getRawDAR = function ($fieldName) use ($submission) {
                            $sd = $submission->submissionData->first(function ($item) use ($fieldName) {
                                return ($item->field->field_name ?? '') === $fieldName;
                            });
                            if ($sd) {
                                if ($sd->field_value_json)
                                    return $sd->field_value_json;
                                return $sd->field_value;
                            }
                            return $submission->field_responses[$fieldName] ?? '';
                        };

                        // Helper to check if DAR checkbox is checked
                        $isCheckedDAR = function ($fieldName) use ($getRawDAR) {
                            $value = $getRawDAR($fieldName);
                            if (is_array($value)) {
                                // Any non-empty array means checkbox is checked
                                return !empty($value);
                            }
                            return !empty($value) && $value !== '0' && $value !== 'false' && $value !== 'no';
                        };

                        // Account Type checkboxes
                        $accSavings = $isCheckedDAR('field_4_1');
                        $accCurrent = $isCheckedDAR('field_4_2');
                        $accFCy = $isCheckedDAR('field_4_3');
                        $accFixed = $isCheckedDAR('field_4_4');
                        $accCredit = $isCheckedDAR('field_4_5');
                        $accFinancing = $isCheckedDAR('field_4_6');
                        $accArRahnu = $isCheckedDAR('field_4_7');
                        $accOthers = $isCheckedDAR('field_4_8');
                        $accOthersSpec = $getRawDAR('field_4_8_1');
                        $accountNo = $getRawDAR('field_4_9');

                        // Personal Data Type checkboxes
                        $dataMandatee = $isCheckedDAR('field_4_10');
                        $dataSignature = $isCheckedDAR('field_4_11');
                        $dataSignatureName = $getRawDAR('field_4_11_1');
                        $dataName = $isCheckedDAR('field_4_12');
                        $dataIC = $isCheckedDAR('field_4_13');
                        $dataAddress = $isCheckedDAR('field_4_14');
                        $dataContact = $isCheckedDAR('field_4_15');
                        $dataGender = $isCheckedDAR('field_4_16');
                        $dataRace = $isCheckedDAR('field_4_17');
                        $dataNationality = $isCheckedDAR('field_4_18');
                        $dataTaxRes = $isCheckedDAR('field_4_19');
                        $dataEmployer = $isCheckedDAR('field_4_20');
                        $dataConsent = $isCheckedDAR('field_4_21');
                        $dataOthers = $isCheckedDAR('field_4_22');
                        $dataOthersSpec = $getRawDAR('field_4_22_1');

                        // Section 3 checkboxes
                        $confirmData = $isCheckedDAR('field_4_23');
                        $supplyData = $isCheckedDAR('field_4_24');

                        // Part E: Method of Delivery
                        $deliveryMail = $isCheckedDAR('field_5_1');
                        $deliveryCollect = $isCheckedDAR('field_5_2');
                        $deliveryCollectBranch = $getRawDAR('field_5_2_1');
                    @endphp

                    <div style="border: 1px solid #000; padding: 10px; font-size: 6pt; margin-bottom: 4px;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">PART D: DESCRIPTION OF PERSONAL DATA REQUESTED</div>

                        {{-- Section 1: Account Type --}}
                        <div style="margin-bottom: 3px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">1. &nbsp;&nbsp;&nbsp;I would like to request for the personal data of the following account by indicating the relevant account number:</div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; border: 1px solid #000;">
                                <tr>
                                    <td style="border: 1px solid #000; padding: 2px; width: 20%; vertical-align: top;">
                                        <div style="font-weight: bold; margin-bottom: 1px;">Account Type</div>
                                        <div style="font-size: 6pt;">(Please tick [✓] one Account only)</div>
                                    </td>
                                    <td style="border: 1px solid #000; padding: 2px; width: 40%; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                                        {{ $accSavings ? '✓' : '' }}
                                                    </div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Savings Account</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                                        {{ $accFCy ? '✓' : '' }}
                                                    </div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">FCy Current Account</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                                        {{ $accCredit ? '✓' : '' }}
                                                    </div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Credit Card Account</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                                        {{ $accArRahnu ? '✓' : '' }}
                                                    </div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Ar Rahnu Account</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border: 1px solid #000; padding: 3px; width: 40%; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $accCurrent ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Current Account</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $accFixed ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Fixed Term Account</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $accFinancing ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Financing Account</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 2px;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $accOthers ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">
                                                    Other products/services (please specify)
                                                    <div style="border-bottom: 1px solid #000; min-height: 14px; margin-top: 2px;">
                                                        {{ $accOthersSpec }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px; font-weight: bold;">Account No.</td>
                                    <td colspan="2" style="border: 1px solid #000; padding: 3px;">{{ $accountNo }}</td>
                                </tr>
                            </table>

                            <div style="font-size: 6pt; font-style: italic; margin-bottom: 4px;">
                                <strong>Note:</strong> For the Requestor requesting access to multiple accounts with the branch, complete a separate form for each account.
                            </div>
                        </div>

                        {{-- Section 2: Personal Data Types --}}
                        <div style="margin-bottom: 3px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">2. &nbsp;&nbsp;&nbsp;Personal Data includes one or more of the following:</div>
                            <div style="margin-bottom: 1px; margin-left: 20px;">Please tick [✓] the appropriate box.</div>
                            <div style="margin-bottom: 1px; margin-left: 20px; font-weight: bold;">Specified Account Information</div>

                            <table style="width: 100%; margin-left: 20px;">
                                {{-- First two checkboxes span both columns --}}
                                <tr>
                                    <td colspan="2" style="padding: 2px 0;">
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataMandatee ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Mandatee, if applicable</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding: 2px 0;">
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataSignature ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Image of the Signature(s) of the account-holder(s) i.e. (specify the name)<span style="border-bottom: 1px dotted #000; min-width: 150px; display: inline-block; margin-left: 5px;">{{ $dataSignatureName }}</span></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                {{-- Two-column layout for remaining checkboxes --}}
                                <tr>
                                    <td style="width: 50%; vertical-align: top; padding-top: 5px;">
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataName ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Name</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataAddress ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Residence/Mailing* Address</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataGender ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Gender</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataNationality ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Nationality</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataEmployer ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Name of Employer</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataOthers ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Others, please specify: <span style="border-bottom: 1px dotted #000; min-width: 100px; display: inline-block;">{{ $dataOthersSpec }}</span></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 50%; vertical-align: top; padding-top: 5px;">
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataIC ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">IC/Passport/Other Identification Documentation*</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataContact ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Contact Details</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataRace ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Race</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataTaxRes ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Country of Tax Residence</td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $dataConsent ? '✓' : '' }}</div>
                                                </td>
                                                <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Customer's Consent</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- Section 3: Request Type --}}
                        <div style="margin-bottom: 4px;">
                            <div style="font-weight: bold; margin-bottom: 4px;">3. &nbsp;&nbsp;&nbsp;Please:-</div>
                            <div style="margin-left: 20px; margin-bottom: 3px;">
                                <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                    <tr>
                                        <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                            <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $confirmData ? '✓' : '' }}</div>
                                        </td>
                                        <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Confirm whether the personal data as specified under items 1 and/ or 2 of Part D is held by the Bank; and I do not require a copy of the Personal Data.</td>
                                    </tr>
                                </table>
                            </div>
                            <div style="margin-left: 20px;">
                                <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                    <tr>
                                        <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                            <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $supplyData ? '✓' : '' }}</div>
                                        </td>
                                        <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">Supply me with a copy of the personal data for the account(s) maintained with the Bank as specified under items 1 and/or 2 of Part D.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Part E: Method of Delivery --}}
                    <div style="border: 1px solid #000; padding: 10px; font-size: 6pt; margin-bottom: 4px;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 4px;">PART E : METHOD OF DELIVERY
                        </div>
                        <div style="margin-bottom: 4px;">The personal data requested :</div>

                        <div style="margin-bottom: 3px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $deliveryMail ? '✓' : '' }}</div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">is to be
                                        mailed to my address stated above.</td>
                                </tr>
                            </table>
                        </div>
                        <div style="margin-bottom: 3px;">
                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                <tr>
                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                        <div style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">{{ $deliveryCollect ? '✓' : '' }}</div>
                                    </td>
                                    <td style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-size: 6pt;">will be collected by me personally from your office / branch at:
                                        <span style="border-bottom: 1px dotted #000; min-width: 200px; display: inline-block; margin-left: 5px;">{{ $deliveryCollectBranch }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Part F: Declaration --}}
                    @php
                        $declName = $getRawDAR('field_6_1') ?? '';
                        $declIC = $getRawDAR('field_6_2') ?? '';
                        $declSigPath = $getRawDAR('field_6_3') ?? '';
                        if (is_string($declSigPath)) {
                            $declSigPath = str_replace('storage/', '', $declSigPath);
                        }
                    @endphp
                    <div style="border: 1px solid #000; padding: 10px; font-size: 6pt;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">PART F : DECLARATION</div>
                        <div style="margin-bottom: 10px;">(by Data Subject (account holder) / Third Party Requestor)</div>

                        <div style="margin-bottom: 10px; line-height: 1.2; text-align: justify;">I, <span style="display: inline-block; min-width: 180px; border-bottom: 1px dotted #000; text-align: center; font-weight: bold;">{{ $declName }}</span>(NRIC / Passport No: <span style="display: inline-block; min-width: 120px; border-bottom: 1px dotted #000; text-align: center; font-weight: bold;">{{ $declIC }}</span>) hereby certify that the information given in this form and all documents enclosed are true and accurate. I understand that it will be necessary for the Bank to verify my identity, and the Bank may contact me for more detailed information in order to locate the personal data requested.</div>

                        <div style="margin-top: 5px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 60%; vertical-align: bottom; padding-bottom: 3px;">
                                        <div style="border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; height: 30px; margin-bottom: 3px;">
                                            @if($declSigPath)
                                                <img src="{{ public_path('storage/' . $declSigPath) }}" alt="Signature" style="max-height: 30px; max-width: 180px;">
                                            @endif
                                        </div>
                                        <div style="font-size: 6pt;">(Signature of Data Subject (account holder) / Third Party Requestor)</div>
                                    </td>
                                    <td style="width: 40%; vertical-align: bottom; text-align: right; padding-bottom: 3px;">
                                        <div>Date: <span style="display: inline-block; min-width: 100px; border-bottom: 1px dotted #000; text-align: center;">{{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y') : date('d/m/Y') }}</span></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif

            @elseif($submission->form->slug === 'dcr' && (stripos($sectionName, 'correction') !== false || stripos($sectionName, 'part d') !== false))
                @continue

            @elseif(stripos($sectionName, 'declaration') !== false && $submission->form->slug === 'dcr')
                @php
                    $declName = collect($fields)->firstWhere('label', 'Full Name (as per NRIC)')['value'] ?? '';
                    $declIC = collect($fields)->firstWhere('label', 'NRIC/Passport No.')['value'] ?? '';
                    $declSigField = collect($fields)->firstWhere('label', 'Signature');
                    $declSigPath = $declSigField ? ($declSigField['value'] ?? '') : '';
                    if (is_string($declSigPath)) {
                        $declSigPath = str_replace('storage/', '', $declSigPath);
                    }
                @endphp
                <div style="border: 1px solid #000; padding: 10px; font-size: 6pt; background: #fff;">
                    <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">PART E : DECLARATION</div>
                    <div style="margin-bottom: 5px;">(by Data Subject (account holder) / Third Party Requestor)</div>

                    <div style="margin-bottom: 3px; line-height: 1.2; text-align: justify;">
                        I, <span style="display: inline-block; min-width: 180px; border-bottom: 1px dotted #000; text-align: center; font-weight: bold;">{{ $declName }}</span> (NRIC / Passport No: <span style="display: inline-block; min-width: 120px; border-bottom: 1px dotted #000; text-align: center; font-weight: bold;">{{ $declIC }}</span>) hereby certify that the information given in this form and all documents enclosed are true and accurate. I understand that it will be necessary for the Bank to verify my identity , and the Bank may contact me for more detailed information in order to locate the personal data requested.
                    </div>

                    <div style="margin-top: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 60%; vertical-align: bottom; padding-bottom: 3px;">
                                    <div style="border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; height: 30px; margin-bottom: 3px;">
                                        @if($declSigPath)
                                            <img src="{{ public_path('storage/' . $declSigPath) }}" alt="Signature" style="max-height: 30px; max-width: 180px;">
                                        @endif
                                    </div>
                                    <div style="font-size: 6pt;">(Signature of Data Subject (account holder) / Third Party Requestor)</div>
                                </td>
                                <td style="width: 40%; vertical-align: bottom; text-align: right; padding-bottom: 3px;">
                                    <div>Date: <span style="display: inline-block; min-width: 100px; border-bottom: 1px dotted #000; text-align: center;">{{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y') : date('d/m/Y') }}</span></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            @elseif(stripos($sectionName, 'personal information') !== false){{-- Split into Part A, B, C --}}
                @php
                    $partAFields = [
                        'I am a customer / former customer and I would like to correct my personal data',
                        'I am a Third Party Requestor'
                    ];

                    $partBFields = [
                        'Full Name (as per NRIC)',
                        'NRIC / Passport No.',
                        'Address',
                        'Postcode',
                        'Email Address',
                        'Telephone No. (Office/Home)',
                        'Mobile No.'
                    ];

                    // Identify Part C fields (everything else not in A or B)
                    $partCFields = [];
                    foreach ($fields as $field) {
                        if (!in_array($field['label'], $partAFields) && !in_array($field['label'], $partBFields)) {
                            $partCFields[] = $field['label'];
                        }
                    }

                    $parts = [
                        'PART A : ABOUT YOURSELF' => $partAFields,
                        'PART B : PARTICULARS OF THE DATA SUBJECT (ACCOUNT HOLDER)' => $partBFields,
                        'PART C : PARTICULARS OF THIRD PARTY REQUESTOR' => $partCFields
                    ];
                @endphp

                @foreach($parts as $partTitle => $targetLabels)
                    <div style="margin-bottom: 5px; page-break-inside: avoid;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 6pt; border: 1px solid #d1d5db;">
                            {{-- Render Part Header --}}
                            <tr>
                                <td
                                    style="border: 1px solid #d1d5db; padding: 4px 6px; background: #fff; font-weight: bold; text-decoration: underline; border-bottom: 1px solid #d1d5db; font-size: 7pt;">
                                    {{ $partTitle }}
                                </td>
                            </tr>
                        </table>

                        {{-- Fields Table --}}
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
                            @foreach($targetLabels as $label)
                                @php
                                    // Find field by label
                                    $field = collect($fields)->firstWhere('label', $label);

                                    // Determine type and value
                                    // If field missing, assume checkbox (standard for Part A unchecked items)
                                    $type = $field ? $field['type'] : 'checkbox';
                                    $value = $field ? $field['value'] : null;

                                    // Check if it's a checkbox/boolean type for visual box rendering
                                    $isCheckbox = in_array($type, ['checkbox', 'boolean', 'radio']);
                                    $isChecked = !empty($value);
                                @endphp
                                <tr>
                                    <td style="border: 1px solid #d1d5db; padding: 3px 5px; width: 100%; background: #fff;">
                                        @if($isCheckbox)
                                            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                                <tr>
                                                    <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                        @if($isChecked)
                                                            <div
                                                                style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; text-align: center; line-height: 10px; font-size: 10px; color: #000;">
                                                                ✓</div>
                                                        @else
                                                            <div
                                                                style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; background-color: #fff;">
                                                                &nbsp;</div>
                                                        @endif
                                                    </td>
                                                    <td
                                                        style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-family: DejaVu Sans, sans-serif;">
                                                        {{ $label }}
                                                    </td>
                                                </tr>
                                        </table> @else
                                            {{-- Render as standard text field --}}
                                            <div>
                                                <strong>{{ $label }}:</strong>
                                                <span
                                                    style="margin-left: 5px;">{{ is_array($value) ? implode(', ', $value) : ($value ?? '-') }}</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endforeach
            @else
                {{-- Standard 2-column layout for other sections --}}
                <table
                    style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid {{ $submission->form->slug === 'srf' ? '#000' : '#d1d5db' }}; border-top: none;">
                    @foreach($fields as $field)
                        @if(FormSubmissionPresenter::shouldDisplayField($field['field_name'], $field['value']))
                            <tr>
                                <td
                                    style="border: 1px solid {{ $submission->form->slug === 'srf' ? '#000' : '#e5e7eb' }}; padding: 3px 5px; width: 40%; background: #fafafa; vertical-align: top;">
                                    <strong>{{ $field['label'] }}</strong>
                                </td>
                                <td
                                    style="border: 1px solid {{ $submission->form->slug === 'srf' ? '#000' : '#e5e7eb' }}; padding: 3px 5px; width: 60%; vertical-align: top;">
                                    @if($field['type'] === 'signature')
                                        <div class="signature-box">
                                            @php
                                                $signaturePath = str_replace('storage/', '', $field['value']);
                                            @endphp
                                            <img src="{{ public_path('storage/' . $signaturePath) }}" alt="Signature">
                                        </div>
                                    @elseif($field['type'] === 'file')
                                        📎 File Attached: {{ basename($field['value']) }}
                                    @elseif(in_array($field['type'], ['boolean', 'checkbox', 'radio']))
                                        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                                            <tr>
                                                <td style="width: 20px; vertical-align: top; padding: 1px 0; border: none;">
                                                    @if($field['value'] && (is_array($field['value']) || (strtolower($field['value']) !== 'no' && strtolower($field['value']) !== 'false')))
                                                        <div
                                                            style="display: inline-block; width: 14px; height: 14px; border: 1px solid #000; text-align: center; line-height: 12px; font-size: 10px; color: #000;">
                                                            ✓</div>
                                                    @else
                                                        <div
                                                            style="display: inline-block; width: 14px; height: 14px; border: 1px solid #000; background-color: #fff;">
                                                            &nbsp;</div>
                                                    @endif
                                                </td>
                                                <td
                                                    style="vertical-align: top; padding: 3px 0 1px 0; border: none; font-family: DejaVu Sans, sans-serif;">
                                                    {{-- Display the value next to the checkbox if it's not just a boolean Yes/No field --}}
                                                    @if($field['type'] !== 'boolean')
                                                        {{ is_array($field['value']) ? implode(', ', $field['value']) : ($field['value'] ?? '') }}
                                                    @else
                                                        {{-- If it's a boolean field and no label text is next to it, we might want to show the
                                                        label?
                                                        However in the current logic, the label is in the left column.
                                                        If this space is empty it looks fine.
                                                        But if there is text to be shown alongside the box inside this cell: --}}
                                                        &nbsp;
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    @elseif(is_array($field['value']))
                                        {{ implode(', ', $field['value']) }}
                                    @else
                                        {{ $field['value'] ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @endif
        </div>
    @endforeach



    {{-- Staff-Only Sections for PDF --}}
    @if($submission->acknowledgment_received_by || $submission->verification_verified_by)
        @if(false)
            {{-- REMOVED DCR SPECIFIC LAYOUT TO UNIFY DESIGN --}}
        @else
            <div class="form-section">
                {{-- Unified Office Use Section (Consistent Design for DAR and DCR) --}}
                @if($submission->acknowledgment_received_by || $submission->verification_verified_by)
                    @php
                        $isDAR = $submission->form->slug === 'dar';
                        $partAckLabel = $isDAR ? 'PART G' : 'PART F';
                        $partVerLabel = $isDAR ? 'PART H' : 'PART G';
                    @endphp

                    <div style="border: 1px solid #000;border-top: none; padding: 10px; font-size: 6pt;">
                        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px; font-size: 7pt;">FOR BMMB OFFICE USE ONLY</div>

                        {{-- Acknowledgment Receipt --}}
                        @if($submission->acknowledgment_received_by)
                            <div style="margin-bottom: 10px;">
                                <div style="font-weight: bold; margin-bottom: 3px; text-transform: uppercase; text-decoration: underline;">
                                    {{ $partAckLabel }} : ACKNOWLEDGMENT RECEIPT
                                </div>
                                <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                    <tr>
                                        {{-- Col 1: Label Left --}}
                                        <td style="width: 18%; padding-bottom: 5px; white-space: nowrap; vertical-align: top;">
                                            Received by:
                                        </td>
                                        {{-- Col 2: Value Left --}}
                                        <td style="width: 42%; padding-bottom: 5px; vertical-align: top;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->acknowledgment_received_by }}
                                            </div>
                                            <div style="font-size: 6pt; font-style: italic;">(signature of staff receiving the correction request)</div>
                                        </td>
                                        {{-- Col 3: Label Right --}}
                                        <td style="width: 15%; padding-bottom: 5px; padding-left: 10px; white-space: nowrap; vertical-align: top; text-align: right;">
                                            Date Received:
                                        </td>
                                        {{-- Col 4: Value Right --}}
                                        <td style="width: 25%; padding-bottom: 3px; vertical-align: top;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px; text-align: left;">
                                                {{ $submission->acknowledgment_date_received ? $submission->acknowledgment_date_received->format('d/m/Y') : '' }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-bottom: 5px; vertical-align: bottom;">Name:</td>
                                        <td style="padding-bottom: 5px; vertical-align: bottom;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->acknowledgment_staff_name }}
                                            </div>
                                        </td>
                                        <td style="padding-bottom: 5px; padding-left: 10px; text-align: right; vertical-align: bottom;">Designation:</td>
                                        <td style="padding-bottom: 5px; vertical-align: bottom;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->acknowledgment_designation }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 15px; vertical-align: bottom; white-space: nowrap;">Official Rubber Stamp:</td>
                                        <td colspan="3" style="padding-top: 15px; vertical-align: bottom;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->acknowledgment_stamp }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div style="border: 1px solid #000; padding: 10px; border-top: none; font-size: 6pt; margin-bottom: 4px;">
                        {{-- Verification --}}
                        @if($submission->verification_verified_by)
                            <div style="margin-bottom: 5px;">
                                <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase; text-decoration: underline;">{{ $partVerLabel }} : VERIFICATION</div>
                                <table style="width: 100%; border-collapse: collapse; font-size: 6pt;">
                                    <tr>
                                        {{-- Col 1: Label Left --}}
                                        <td style="width: 18%; padding-bottom: 5px; white-space: nowrap; vertical-align: top;">Verified by:</td>
                                        {{-- Col 2: Value Left --}}
                                        <td style="width: 42%; padding-bottom: 5px; vertical-align: top;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->verification_verified_by }}
                                            </div>
                                            <div style="font-size: 6pt; font-style: italic;">(signature of staff verifying the correction request)</div>
                                        </td>
                                        {{-- Col 3: Label Right --}}
                                        <td style="width: 15%; padding-bottom: 5px; padding-left: 10px; white-space: nowrap; vertical-align: top; text-align: right;">Date:</td>
                                        {{-- Col 4: Value Right --}}
                                        <td style="width: 25%; padding-bottom: 5px; vertical-align: top;">
                                            <div
                                                style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px; text-align: left;">
                                                {{ $submission->verification_date ? $submission->verification_date->format('d/m/Y') : '' }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-bottom: 5px; vertical-align: bottom;">Name:</td>
                                        <td style="padding-bottom: 5px; vertical-align: bottom;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->verification_staff_name }}
                                            </div>
                                        </td>
                                        <td style="padding-bottom: 5px; padding-left: 10px; text-align: right; vertical-align: bottom;">Designation:</td>
                                        <td style="padding-bottom: 5px; vertical-align: bottom;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->verification_designation }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 15px; vertical-align: bottom; white-space: nowrap;">Official Rubber Stamp:</td>
                                        <td colspan="3" style="padding-top: 15px; vertical-align: bottom;">
                                            <div style="border-bottom: 1px dotted #000; width: 100%; min-height: 14px;">
                                                {{ $submission->verification_stamp }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- Footer Note -->
    <div class="footer-note">
        <strong>NOTE:</strong> This is an electronically generated document and does not require manual signature.
        Please quote the Reference Number above for any inquiries related to this submission.
    </div>
</body>

</html>