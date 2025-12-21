<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $submission->form->name }} - Receipt #{{ $submission->reference_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --accent-color: #10b981;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --text-light: #9ca3af;
            --bg-light: #f9fafb;
            --bg-highlight: #eff6ff;
            --border-color: #e5e7eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: #ffffff;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
        }

        /* Header Styling */
        .document-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .document-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .bank-logo {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 3px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .document-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            opacity: 0.95;
        }

        .document-subtitle {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 300;
        }

        /* Reference Number Badge */
        .ref-badge-container {
            background: var(--bg-highlight);
            border: 2px dashed var(--secondary-color);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
        }

        .ref-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-gray);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .ref-number {
            font-family: 'Courier New', monospace;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .ref-hint {
            font-size: 11px;
            color: var(--text-light);
            font-style: italic;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .info-card {
            background: var(--bg-light);
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--secondary-color);
        }

        .info-card.status-card {
            border-left-color: var(--success-color);
        }

        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-gray);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: capitalize;
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* Section Styling */
        .form-section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }

        .section-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.15);
        }

        /* Field Styling */
        .field-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .field-row.full-width {
            grid-template-columns: 1fr;
        }

        .field-group {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px;
            transition: all 0.3s ease;
        }

        .field-group:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: var(--secondary-color);
        }

        .field-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-gray);
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .field-label::before {
            content: '';
            width: 4px;
            height: 12px;
            background: var(--accent-color);
            margin-right: 8px;
            border-radius: 2px;
        }

        .field-value {
            font-size: 15px;
            color: var(--text-dark);
            font-weight: 500;
            min-height: 24px;
            padding: 4px 0;
        }

        .field-value.empty {
            color: var(--text-light);
            font-style: italic;
        }

        .field-value.highlight {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Signature Styling */
        .signature-box {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 15px 0;
        }

        .signature-img {
            max-width: 350px;
            max-height: 150px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px;
            background: #fafafa;
        }

        /* Service Checklist */
        .service-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 15px 0;
        }

        .service-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: var(--bg-light);
            border-radius: 6px;
            border-left: 3px solid var(--accent-color);
        }

        .service-item .check-icon {
            width: 20px;
            height: 20px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-center;
            margin-right: 12px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .service-item .service-name {
            font-size: 14px;
            color: var(--text-dark);
            font-weight: 500;
        }

        /* Footer */
        .document-footer {
            background: var(--bg-light);
            padding: 25px;
            border-radius: 10px;
            margin-top: 40px;
            border-top: 3px solid var(--primary-color);
        }

        .footer-content {
            text-align: center;
        }

        .footer-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .footer-text {
            font-size: 11px;
            color: var(--text-gray);
            line-height: 1.8;
        }

        .footer-contact {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .contact-info {
            display: inline-flex;
            align-items: center;
            margin: 0 15px;
            font-size: 11px;
            color: var(--text-gray);
        }

        /* Print Specific */
        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .document-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .field-group:hover {
                box-shadow: none;
            }

            @page {
                margin: 15mm;
                size: A4;
            }

            .form-section {
                page-break-inside: avoid;
            }
        }

        /* Floating Action Bar */
        .action-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .action-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .action-btn.download {
            background: var(--success-color);
        }

        .action-btn.close {
            background: #6b7280;
        }

        .action-btn svg {
            width: 18px;
            height: 18px;
        }
    </style>
</head>

<body>
    <!-- Floating Action Bar -->
    <div class="action-bar no-print">
        <button onclick="window.print()" class="action-btn">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z" />
            </svg>
            Print
        </button>
        <button onclick="window.print()" class="action-btn download">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
            </svg>
            Download
        </button>
        <button onclick="window.close()" class="action-btn close">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
            </svg>
            Close
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="document-header">
            <div class="header-content">
                <div class="bank-logo">BMMB</div>
                <div class="document-title">{{ $submission->form->name }}</div>
                <div class="document-subtitle">Official Submission Receipt</div>
            </div>
        </div>

        <!-- Reference Number Badge -->
        <div class="ref-badge-container">
            <div class="ref-label">Reference Number</div>
            <div class="ref-number">{{ $submission->reference_number ?? 'N/A' }}</div>
            <div class="ref-hint">Save this number for future reference</div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Submission ID</div>
                <div class="info-value">#{{ $submission->id }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Submitted On</div>
                <div class="info-value">{{ $submission->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="info-card status-card">
                <div class="info-label">Status</div>
                <div>
                    <span
                        class="status-badge {{ in_array($submission->status, ['submitted', 'approved', 'completed']) ? '' : 'pending' }}">
                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Data -->
        @php
            use App\Services\FormSubmissionPresenter;
            $formattedData = FormSubmissionPresenter::formatSubmissionData($submission);
            $formSlug = $submission->form->slug;
        @endphp

        @foreach ($formattedData as $sectionName => $fields)
            <div class="form-section">
                <div class="section-header">{{ $sectionName }}</div>

                @php
                    $fieldCount = 0;
                    $currentRow = [];
                @endphp

                @foreach ($fields as $field)
                    @if (FormSubmissionPresenter::shouldDisplayField($field['field_name'], $field['value']))
                        @php
                            $currentRow[] = $field;
                            $fieldCount++;

                            // Determine if field should be full width
                            $isFullWidth = in_array($field['type'], ['text_long', 'textarea', 'signature', 'file']) ||
                                strlen($field['value'] ?? '') > 100;
                        @endphp

                        @if ($isFullWidth || count($currentRow) == 2 || $loop->last)
                            <div class="field-row {{ $isFullWidth ? 'full-width' : '' }}">
                                @foreach ($currentRow as $rowField)
                                    <div class="field-group">
                                        <div class="field-label">{{ $rowField['label'] }}</div>
                                        <div class="field-value {{ empty($rowField['value']) ? 'empty' : '' }}">
                                            @if ($rowField['type'] === 'signature')
                                                <div class="signature-box">
                                                    @php
                                                        $signaturePath = str_replace('storage/', '', $rowField['value']);
                                                    @endphp
                                                    <img src="{{ asset('storage/' . $signaturePath) }}" alt="Signature" class="signature-img">
                                                </div>
                                            @elseif($rowField['type'] === 'file')
                                                📎 File attached
                                            @elseif($rowField['type'] === 'boolean')
                                                <span class="{{ $rowField['value'] ? 'highlight' : '' }}">
                                                    {{ $rowField['value'] ? '✓ Yes' : '✗ No' }}
                                                </span>
                                            @elseif(is_array($rowField['value']))
                                                {{ implode(', ', $rowField['value']) }}
                                            @else
                                                {{ $rowField['value'] ?? 'Not provided' }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @php
                                $currentRow = [];
                            @endphp
                        @endif
                    @endif
                @endforeach
            </div>
        @endforeach

        <!-- Footer -->
        <div class="document-footer">
            <div class="footer-content">
                <div class="footer-title">📋 Official Submission Receipt</div>
                <div class="footer-text">
                    This is an official submission receipt from BMMB Digital Forms System.<br>
                    Generated on {{ now()->format('d M Y, h:i A') }} GMT+8
                </div>
                <div class="footer-contact">
                    <span class="contact-info">
                        ✉️ support@bmmb.com.my
                    </span>
                    <span class="contact-info">
                        📞 03-2161 1000
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>