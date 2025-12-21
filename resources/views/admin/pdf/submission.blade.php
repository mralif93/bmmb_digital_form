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
    <div class="document-header">
        <div class="document-title">{{ strtoupper($submission->form->name) }}</div>
        <div class="document-subtitle">BMMB Digital Forms - Official Submission Receipt</div>
        <div class="document-meta">Generated: {{ now()->format('d F Y, h:i A') }}</div>
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

    <!-- Form Data -->
    @php
        use App\Services\FormSubmissionPresenter;
        $formattedData = FormSubmissionPresenter::formatSubmissionData($submission);
    @endphp

    @foreach($formattedData as $sectionName => $fields)
        <div class="form-section">
            <div class="section-header">{{ strtoupper($sectionName) }}</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%">No.</th>
                        <th style="width: 35%">Field</th>
                        <th style="width: 60%">Information</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($fields as $field)
                        @if(FormSubmissionPresenter::shouldDisplayField($field['field_name'], $field['value']))
                            <tr>
                                <td style="text-align: center">{{ $no++ }}</td>
                                <td><strong>{{ $field['label'] }}</strong></td>
                                <td>
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
                </tbody>
            </table>
        </div>
    @endforeach

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