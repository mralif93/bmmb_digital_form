<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $submission->form->name }} - {{ $submission->reference_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            color: #000;
            padding: 15px;
        }

        /* Header Section */
        .document-header {
            background: #ea580c;
            color: white;
            padding: 8px;
            text-align: center;
            margin-bottom: 6px;
        }

        .document-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }

        .document-subtitle {
            font-size: 8pt;
            font-weight: normal;
        }

        .document-meta {
            font-size: 7pt;
            margin-top: 4px;
            opacity: 0.95;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 6px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 5px;
            border: 1px solid #ddd;
        }

        .info-label {
            background: #f3f4f6;
            font-weight: bold;
            width: 160px;
            font-size: 7pt;
            text-transform: uppercase;
            color: #374151;
        }

        .info-value {
            font-size: 8pt;
            color: #000;
        }

        .ref-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 9pt;
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
            padding: 3px 4px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            border: 1px solid #c2410c;
        }

        .data-table td {
            padding: 3px 4px;
            border: 1px solid #ddd;
            font-size: 7pt;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .field-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 3px;
        }

        .field-value {
            color: #000;
        }

        /* Section Headers */
        .section-header {
            background: #c2410c;
            color: white;
            padding: 4px 8px;
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
            min-height: 100px;
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
    </style>
</head>

<body>
    <!-- Header -->
    <div class="document-header" style="position: relative; padding: 10px 15px;">
        <table style="width: 100%; border: 0;">
            <tr>
                <td style="width: 70%; vertical-align: middle; border: 0;">
                    <div class="document-title" style="text-align: left; margin-bottom: 3px;">
                        {{ strtoupper($submission->form->name) }}
                    </div>
                    <div class="document-subtitle" style="text-align: left; font-size: 7pt;">BMMB Digital Forms -
                        Official Submission Receipt</div>
                    <div class="document-meta" style="text-align: left; font-size: 6pt; margin-top: 3px;">Generated:
                        {{ now()->format('d F Y, h:i A') }}
                    </div>
                </td>
                <td style="width: 30%; vertical-align: middle; text-align: right; border: 0;">
                    <img src="{{ public_path('assets/images/logo-bmmb-white.png') }}" alt="Bank Muamalat"
                        style="max-height: 80px; max-width: 180px; display: inline-block;">
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
        <div class="form-section" style="margin-top: 10px;">
            <div
                style="background: #ea580c; color: white; padding: 4px 8px; font-size: 9pt; font-weight: bold; margin-bottom: 2px;">
                IMPORTANT NOTE:
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 7pt; border: 1px solid #ddd;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 6px; vertical-align: top;">
                        <ol style="margin: 0; padding-left: 18px; line-height: 1.6;">
                            <li style="margin-bottom: 4px;">Please complete the Data Correction Request Form and ensure that
                                your personal data provided herein is genuine and accurate.</li>
                            <li style="margin-bottom: 4px;">Your request may not be processed if the information/document
                                provided is incomplete.</li>
                            <li style="margin-bottom: 4px;">Third Party Requestor is to be present at the branch / office to
                                submit this form and for verification of information and documents required.</li>
                            <li style="margin-bottom: 4px;">The supporting document(s) required in this form must be
                                provided. We will respond within 21 days of receipt of the completed form with accompanying
                                documents.</li>
                            <li style="margin-bottom: 4px;">If you have any queries / need any guidance in filling-up this
                                form, you may contact our Customer Service Department at the contact details below:</li>
                        </ol>
                        <p style="margin: 2px 0 2px 18px; font-style: italic;"><strong>Head, Customer Service Department,
                                Bank Muamalat Malaysia Berhad</strong></p>
                        <p style="margin: 2px 0 0 18px;">
                            <strong>Address:</strong> 19th Floor, Menara Bumiputra, Jalan Melaka, 51000 Kuala Lumpur<br>
                            <strong>Telephone:</strong> 1-300-88-8787 (Local), +603-26005500 (International)<br>
                            <strong>Email:</strong> feedback@muamalat.com.my
                        </p>
                    </td>
                </tr>
            </table>
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

        <div class="form-section" style="margin-top: 10px;">
            {{-- Section Header --}}
            <div
                style="background: #ea580c; color: white; padding: 6px 10px; font-size: 9pt; font-weight: bold; border: 1px solid #c2410c; border-bottom: none;">
                {{ strtoupper($sectionName) }}
            </div>

            {{-- Special 3-column layout for Data Correction Details --}}
            @if(stripos($sectionName, 'correction') !== false || stripos($sectionName, 'part d') !== false)
                <table style="width: 100%; border-collapse: collapse; font-size: 7pt; border: 1px solid #000;">
                    <thead>
                        <tr style="background: #fff;">
                            <th rowspan="2"
                                style="border: 1px solid #000; padding: 8px; text-align: center; width: 25%; font-weight: bold; vertical-align: middle;">
                                PERSONAL DATA TYPE
                            </th>
                            <th rowspan="2"
                                style="border: 1px solid #000; padding: 8px; text-align: center; width: 50%; font-weight: bold; vertical-align: middle;">
                                PLEASE PROVIDE THE PERSONAL DATA TO BE CORRECTED
                            </th>
                            <th colspan="3"
                                style="border: 1px solid #000; padding: 4px 8px; text-align: center; font-weight: bold;">
                                Please Tick (√) the Appropriate Column
                            </th>
                        </tr>
                        <tr style="background: #fff;">
                            <th
                                style="border: 1px solid #000; padding: 4px; text-align: center; width: 8.33%; font-weight: bold;">
                                A</th>
                            <th
                                style="border: 1px solid #000; padding: 4px; text-align: center; width: 8.33%; font-weight: bold;">
                                D</th>
                            <th
                                style="border: 1px solid #000; padding: 4px; text-align: center; width: 8.33%; font-weight: bold;">
                                R</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Define the specific fields to display for Data Correction Details
                            $correctionFields = [
                                'Name of Data Subject (account holder)',
                                'Old IC No.',
                                'New IC No.',
                                'Passport No.',
                                'Residential/Mailing Address*',
                                'Postcode',
                                'Account Number',
                                'Telephone No. (House)',
                                'Telephone No. (Office)',
                                'Mobile Phone Number',
                                'Nationality',
                                'Occupation',
                                'Name of Employer',
                                'Others (Please specify)'
                            ];

                            // Create a map of field labels to their values
                            $fieldMap = [];
                            foreach ($fields as $field) {
                                $fieldMap[$field['label']] = $field;
                            }
                        @endphp

                        @foreach($correctionFields as $fieldLabel)
                            <tr>
                                <td style="border: 1px solid #000; padding: 6px 8px; vertical-align: top;">
                                    <strong>{{ $fieldLabel }}</strong>
                                </td>
                                <td style="border: 1px solid #000; padding: 6px 8px; vertical-align: top;">
                                    {{ $fieldMap[$fieldLabel]['value'] ?? '' }}
                                </td>
                                @php
                                    $actionFieldName = 'Action for ' . $fieldLabel;
                                    $actionValue = '';
                                    if (isset($fieldMap[$actionFieldName])) {
                                        $actionValue = strtoupper($fieldMap[$actionFieldName]['value'] ?? '');
                                    }
                                @endphp
                                <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle;">
                                    {{ $actionValue === 'A' || $actionValue === 'ADD' ? '✓' : '' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle;">
                                    {{ $actionValue === 'D' || $actionValue === 'DELETE' ? '✓' : '' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle;">
                                    {{ $actionValue === 'R' || $actionValue === 'REVISE' ? '✓' : '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div
                    style="font-size: 6pt; font-style: italic; padding: 4px 8px; border: 1px solid #000; border-top: none; background: #fff;">
                    <strong>Note :</strong> A; Add D; delete ; R: Revise
                </div>
            @else
                {{-- Standard 2-column layout for other sections --}}
                <table
                    style="width: 100%; border-collapse: collapse; font-size: 7pt; border: 1px solid #d1d5db; border-top: none;">
                    @foreach($fields as $field)
                        @if(FormSubmissionPresenter::shouldDisplayField($field['field_name'], $field['value']))
                            <tr>
                                <td
                                    style="border: 1px solid #e5e7eb; padding: 6px 8px; width: 40%; background: #fafafa; vertical-align: top;">
                                    <strong>{{ $field['label'] }}</strong>
                                </td>
                                <td style="border: 1px solid #e5e7eb; padding: 6px 8px; width: 60%; vertical-align: top;">
                                    @if($field['type'] === 'signature')
                                        <div class="signature-box">
                                            @php
                                                $signaturePath = str_replace('storage/', '', $field['value']);
                                            @endphp
                                            <img src="{{ public_path('storage/' . $signaturePath) }}" alt="Signature">
                                        </div>
                                    @elseif($field['type'] === 'file')
                                        📎 File Attached: {{ basename($field['value']) }}
                                    @elseif($field['type'] === 'boolean')
                                        {{ $field['value'] ? '✓ Yes' : '✗ No' }}
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
        <div class="form-section" style="background: #fff7ed; border: 2px solid #ea580c; padding: 10px; margin-top: 10px;">
            <div
                style="text-align: center; font-weight: bold; font-size: 9pt; margin-bottom: 8px; text-transform: uppercase; color: #9a3412;">
                FOR BMMB OFFICE USE ONLY
            </div>

            {{-- Acknowledgment Receipt Section --}}
            @if($submission->acknowledgment_received_by)
                @php
                    // Determine part label based on form type
                    $acknowledgmentPart = $submission->form->slug === 'dar' ? 'PART G' : 'PART F';
                @endphp
                <div style="margin-bottom: 8px;">
                    <div
                        style="background: #ea580c; color: white; padding: 3px 6px; font-size: 8pt; font-weight: bold; margin-bottom: 4px;">
                        {{ $acknowledgmentPart }}: ACKNOWLEDGMENT RECEIPT
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 7pt;">
                        <tr>
                            <td
                                style="border: 1px solid #ddd; padding: 4px; width: 30%; background: #f9fafb; font-weight: bold;">
                                Received by:</td>
                            <td style="border: 1px solid #ddd; padding: 4px; width: 70%;">
                                {{ $submission->acknowledgment_received_by }}
                            </td>
                        </tr>
                        @if($submission->acknowledgment_date_received)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">Date
                                    Received:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">
                                    {{ $submission->acknowledgment_date_received->format('d M Y') }}
                                </td>
                            </tr>
                        @endif
                        @if($submission->acknowledgment_staff_name)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">Name:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">{{ $submission->acknowledgment_staff_name }}</td>
                            </tr>
                        @endif
                        @if($submission->acknowledgment_designation)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">
                                    Designation:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">{{ $submission->acknowledgment_designation }}</td>
                            </tr>
                        @endif
                        @if($submission->acknowledgment_stamp)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">Official
                                    Rubber Stamp:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">{{ $submission->acknowledgment_stamp }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @endif

            {{-- Verification Section --}}
            @if($submission->verification_verified_by)
                @php
                    // Determine part label based on form type
                    $verificationPart = $submission->form->slug === 'dar' ? 'PART H' : 'PART G';
                @endphp
                <div
                    style="{{ $submission->acknowledgment_received_by ? 'padding-top: 8px; border-top: 2px dotted #ea580c;' : '' }}">
                    <div
                        style="background: #ea580c; color: white; padding: 3px 6px; font-size: 8pt; font-weight: bold; margin-bottom: 4px;">
                        {{ $verificationPart }}: VERIFICATION
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 7pt;">
                        <tr>
                            <td
                                style="border: 1px solid #ddd; padding: 4px; width: 30%; background: #f9fafb; font-weight: bold;">
                                Verified by:</td>
                            <td style="border: 1px solid #ddd; padding: 4px; width: 70%;">
                                {{ $submission->verification_verified_by }}
                            </td>
                        </tr>
                        @if($submission->verification_date)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">Date:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">
                                    {{ $submission->verification_date->format('d M Y') }}
                                </td>
                            </tr>
                        @endif
                        @if($submission->verification_staff_name)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">Name:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">{{ $submission->verification_staff_name }}</td>
                            </tr>
                        @endif
                        @if($submission->verification_designation)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">
                                    Designation:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">{{ $submission->verification_designation }}</td>
                            </tr>
                        @endif
                        @if($submission->verification_stamp)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 4px; background: #f9fafb; font-weight: bold;">
                                    Verification Stamp:</td>
                                <td style="border: 1px solid #ddd; padding: 4px;">{{ $submission->verification_stamp }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- Footer Note -->
    <div class="footer-note">
        <strong>NOTE:</strong> This is an electronically generated document and does not require manual signature.
        Please quote the Reference Number above for any inquiries related to this submission.
    </div>

    <!-- Footer -->
    <div class="document-footer">
        <p><strong>BMMB Digital Forms System</strong></p>
        <p>Bank Muamalat Malaysia Berhad</p>
        <p>Email: support@bmmb.com.my | Phone: 03-2161 1000</p>
        <p style="margin-top: 8px; font-size: 7pt;">
            Document ID: {{ $submission->id }} | Generated: {{ now()->format('d/m/Y H:i:s') }} |
            {{ $submission->reference_number }}
        </p>
    </div>
</body>

</html>