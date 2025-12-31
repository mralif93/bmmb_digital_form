<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Counter Display - {{ $branch->name }}</title>

    @if(isset($isPdf))
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;800&display=swap');

            @page {
                size: 210mm 297mm;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: 'Outfit', sans-serif;
                color: white;
                background:
                    {{ $primaryColor }}
                ;
                width: 210mm;
                height: 297mm;
                overflow: hidden;
            }

            .bg-gradient {
                background:
                    {{ $primaryColor }}
                ;
                position: absolute;
                top: 0;
                left: 0;
                width: 210mm;
                height: 297mm;
                z-index: -1;
                /* Pattern overlay */
                background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 1px);
                background-size: 30px 30px;
            }

            .container {
                padding: 40px;
                text-align: center;
            }

            /* Header */
            .header-table {
                width: 100%;
                margin-bottom: 30px;
            }

            .logo-box {
                width: 45px;
                height: 45px;
                background: white;
                border-radius: 12px;
                text-align: center;
                line-height: 45px;
                color:
                    {{ $primaryColor }}
                ;
                font-size: 22px;
                display: inline-block;
                vertical-align: middle;
            }

            .branch-info {
                display: inline-block;
                vertical-align: middle;
                margin-left: 15px;
                text-align: left;
            }

            h1 {
                font-size: 20px;
                font-weight: 800;
                margin: 0;
                text-transform: uppercase;
                line-height: 1;
                letter-spacing: -0.5px;
            }

            .counter-label {
                font-size: 9px;
                font-weight: 700;
                letter-spacing: 2px;
                opacity: 0.9;
                margin-top: 4px;
                text-transform: uppercase;
            }

            .system-live {
                background: rgba(0, 0, 0, 0.15);
                padding: 6px 14px;
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                display: inline-block;
            }

            .live-dot {
                display: inline-block;
                width: 6px;
                height: 6px;
                background: #22c55e;
                border-radius: 50%;
                margin-right: 6px;
            }

            /* Title */
            .main-title {
                font-size: 42px;
                font-weight: 800;
                margin-bottom: 5px;
                letter-spacing: -1px;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .main-subtitle {
                font-size: 18px;
                font-weight: 500;
                opacity: 0.95;
                margin-bottom: 40px;
            }

            /* QR Card */
            .qr-card {
                background: white;
                border-radius: 40px;
                padding: 40px 30px;
                width: 320px;
                margin: 0 auto 50px;
                color: #1e293b;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
                text-align: center;
                display: block;
                /* Stack vertically */
            }

            .qr-wrapper {
                margin: 0 auto 20px;
                width: 220px;
                height: 220px;
                position: relative;
            }

            .qr-title {
                font-size: 24px;
                font-weight: 800;
                margin: 0 0 5px;
                letter-spacing: -0.5px;
                color: #0f172a;
            }

            .qr-desc {
                font-size: 14px;
                color: #64748b;
                margin: 0 0 20px;
                font-weight: 500;
            }

            .date-pill {
                background: #f1f5f9;
                padding: 8px 16px;
                border-radius: 12px;
                display: inline-block;
            }

            .date-text {
                color: #475569;
                font-size: 13px;
                font-weight: 700;
            }

            /* Steps Container */
            .steps-container {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
            }

            /* Step Box - Compact */
            .step-box {
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 18px;
                padding: 15px 20px;
                margin-bottom: 15px;
                width: 100%;
                text-align: left;
            }

            .step-num {
                display: inline-block;
                width: 40px;
                height: 40px;
                background: white;
                color:
                    {{ $primaryColor }}
                ;
                border-radius: 50%;
                text-align: center;
                line-height: 40px;
                font-weight: 800;
                font-size: 18px;
                vertical-align: middle;
                margin-right: 15px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .step-text {
                display: inline-block;
                vertical-align: middle;
            }

            .step-title {
                font-weight: 700;
                font-size: 18px;
                margin: 0 0 2px;
            }

            .step-desc {
                font-size: 13px;
                font-weight: 400;
                opacity: 0.9;
                margin: 0;
            }

            .footer {
                position: fixed;
                bottom: 30px;
                width: 100%;
                text-align: center;
                font-size: 10px;
                font-weight: 700;
                opacity: 0.6;
                letter-spacing: 2px;
            }
        </style>
    @else
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Auto-refresh -->
        <meta http-equiv="refresh" content="60">
        <script>setTimeout(function () { window.location.reload(); }, 60000);</script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    @endif
</head>

<body class="@if(!isset($isPdf)) min-h-screen text-white selection:bg-white selection:text-orange-600 @endif">

    @if(isset($isPdf))
        <div class="bg-gradient"></div>
        <div class="container">
            <!-- Header Table -->
            <table class="header-table">
                <tr>
                    <td style="text-align: left;">
                        <div class="logo-box"><i class='bx bx-buildings'></i>BM</div>
                        <div class="branch-info">
                            <h1>{{ $branch->name }}</h1>
                            <div class="counter-label">COUNTER DISPLAY</div>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div class="system-live">
                            <span class="live-dot"></span> SYSTEM LIVE
                        </div>
                    </td>
                </tr>
            </table>

            <div class="main-title">Digital eForm Application</div>
            <div class="main-subtitle">Fast. Secure. Paperless.</div>

            <!-- Vertical Layout Stack -->
            <div class="qr-card">
                <div class="qr-wrapper">
                    @if($qrContent)
                        <div style="text-align: center;">
                            <img src="data:image/svg+xml;base64,{{ base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->color(30, 41, 59)->generate($qrContent)) }}"
                                width="220" />
                        </div>
                    @endif

                </div>
                <h2 class="qr-title">Scan to Apply</h2>
                <p class="qr-desc">Point your camera at the code</p>
                <div class="date-pill">
                    <span class="date-text"><img style="vertical-align: middle; margin-right: 5px; width: 14px;"
                            src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy5wMy5vcmcvMjAwMC9zdmciIHZpZXdib3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNDc1NTY5IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHJlY3QgeD0iMyIgeT0iNCIgd2lkdGg9IjE4IiBoZWlnaHQ9IjE4IiByeD0iMiIgcnk9IjIiPjwvcmVjdD48bGluZSB4MT0iMTYiIHkxPSIyIiB4Mj0iMTYiIHkyPSI2Ij48L2xpbmU+PGxpbmUgeDE9IjgiIHkxPSIyIiB4Mj0iOCIgeTI9IjYiPjwvbGluZT48bGluZSB4MT0iMyIgeTE9IjEwIiB4Mj0iMjEiIHkyPSIxMCI+PC9saW5lPjwvc3ZnPg==" />
                        {{ now()->format('d M Y') }}</span>
                </div>
            </div>

            <div class="steps-container">
                @foreach([['num' => 1, 't' => 'Open Camera', 'd' => "Open your phone's camera app"], ['num' => 2, 't' => 'Scan QR Code', 'd' => 'Point your camera at the code'], ['num' => 3, 't' => 'Apply Securely', 'd' => 'Fill out the form on your device']] as $step)
                    <div class="step-box">
                        <div class="step-num">{{ $step['num'] }}</div>
                        <div class="step-text">
                            <div class="step-title">{{ $step['t'] }}</div>
                            <div class="step-desc">{{ $step['d'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} BANK MUAMALAT MALAYSIA BERHAD
            </div>
        </div>
    @else
        <!-- Original Web Layout (Tailwind) -->
        <div class="fixed inset-0 z-0"
            style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }} 100%);">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>
            <div
                class="absolute top-0 right-0 w-[50vw] h-[50vw] bg-white opacity-10 blur-[120px] rounded-full pointer-events-none translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-[40vw] h-[40vw] bg-black opacity-5 blur-[100px] rounded-full pointer-events-none -translate-x-1/3 translate-y-1/4">
            </div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header -->
            <div class="flex-none px-6 py-8 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                        <i class='bx bx-buildings text-2xl' style="color: {{ $primaryColor }}"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-xl leading-none tracking-tight uppercase">{{ $branch->name }}</h1>
                        <p class="text-white/80 text-[11px] font-bold tracking-widest uppercase mt-1">Counter Display</p>
                    </div>
                </div>
                <div
                    class="hidden md:flex items-center gap-2 bg-black/10 px-4 py-2 rounded-full border border-white/10 backdrop-blur-sm">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                    </span>
                    <span class="text-white text-xs font-bold tracking-wide uppercase">System Live</span>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-grow flex flex-col items-center justify-center p-6 md:p-12">
                <div class="w-full max-w-4xl text-center mb-10 md:mb-16">
                    <h2 class="text-4xl md:text-5xl font-black leading-tight mb-4 drop-shadow-sm text-white">
                        Digital eForm <span class="opacity-90">Application</span>
                    </h2>
                    <p class="text-lg md:text-xl text-white/90 font-medium leading-relaxed max-w-2xl mx-auto">Fast. Secure.
                        Paperless.</p>
                </div>

                <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-20 items-start">
                    <!-- Col 1: QR Card (Left) -->
                    <div class="flex justify-center md:justify-end">
                        <div class="relative group w-full max-w-[380px]">
                            <div
                                class="relative bg-white rounded-[2.5rem] p-8 shadow-2xl text-center transform transition hover:scale-[1.02] duration-500">
                                <div class="relative mb-6 mx-auto w-[220px] h-[220px]">
                                    @if($qrContent)
                                        <div id="qrcode"
                                            class="w-full h-full bg-white flex items-center justify-center overflow-hidden">
                                        </div>
                                        <script type="text/javascript">
                                            new QRCode(document.getElementById("qrcode"), {
                                                text: "{!! $qrContent !!}", width: 220, height: 220, colorDark: "#1e293b", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H
                                            });
                                            setTimeout(() => { let qrImg = document.querySelector("#qrcode img"); if (qrImg) qrImg.style.display = "block"; }, 100);
                                        </script>
                                    @else
                                        <div
                                            class="w-full h-full bg-gray-50 rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-gray-200">
                                            <i class='bx bx-loader-alt animate-spin text-4xl mb-3'
                                                style="color: {{ $primaryColor }}"></i>
                                            <p class="text-sm font-bold text-gray-400">Please Wait...</p>
                                        </div>
                                    @endif

                                </div>
                                <div class="space-y-1 mb-6">
                                    <h3 class="text-slate-900 font-extrabold text-2xl tracking-tight">Scan to Apply</h3>
                                    <p class="text-slate-500 font-medium text-base">Point your camera at the code</p>
                                </div>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl">
                                    <i class='bx bx-calendar text-gray-400 text-lg'></i>
                                    <span class="text-slate-600 font-bold text-sm">{{ now()->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Col 2: Instructions (Right) -->
                    <div class="flex flex-col justify-center h-full">
                        <div class="space-y-4 max-w-md mx-auto md:mx-0">
                            @foreach([['num' => 1, 't' => 'Open Camera', 'd' => "Open your phone's camera app"], ['num' => 2, 't' => 'Scan QR Code', 'd' => 'Point your camera at the code'], ['num' => 3, 't' => 'Apply Securely', 'd' => 'Fill out the form on your device']] as $step)
                                <div
                                    class="flex items-center gap-5 p-5 rounded-2xl bg-white/10 border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-sm backdrop-blur-md group">
                                    <div class="flex-none w-12 h-12 rounded-full bg-white text-xl font-bold flex items-center justify-center shadow-md group-hover:scale-110 transition-transform"
                                        style="color: {{ $primaryColor }}">{{ $step['num'] }}</div>
                                    <div class="text-left">
                                        <h4 class="font-bold text-xl leading-none mb-1 text-white">{{ $step['t'] }}</h4>
                                        <p class="text-white/80 text-sm font-medium">{{ $step['d'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-none py-8 text-center">
                <p class="text-white/60 text-xs font-bold tracking-[0.2em] uppercase">&copy; {{ date('Y') }} Bank Muamalat
                    Malaysia Berhad</p>
            </div>
        </div>

        <!-- Download PDF Button -->
        <div class="fixed bottom-6 right-6 z-50 no-print">
            <a href="{{ route('branch.qr-display.pdf') }}" target="_blank"
                class="flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-full shadow-lg hover:bg-gray-800 transition-all font-medium">
                <i class='bx bxs-file-pdf text-xl'></i>
                <span>Download PDF</span>
            </a>
        </div>
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endif
</body>

</html>