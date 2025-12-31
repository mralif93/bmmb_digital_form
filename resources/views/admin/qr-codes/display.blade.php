```
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Counter Display - {{ $branch->name }}</title>

    <!-- Auto-refresh every 60 seconds to keep session alive and QR updated -->
    <meta http-equiv="refresh" content="60">
    <script>
        // Robust fallback: Force reload every 60 seconds
        setTimeout(function () {
            window.location.reload();
        }, 60000);
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen text-white selection:bg-white selection:text-orange-600">

    <!-- Solid Brand Background -->
    <div class="fixed inset-0 z-0"
        style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }} 100%);">
        <!-- Subtle Pattern Overlay -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;">
        </div>
        <!-- Light Glare -->
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

            <!-- Row 1: Title Section -->
            <div class="w-full max-w-4xl text-center mb-10 md:mb-16">
                <h2 class="text-4xl md:text-5xl font-black leading-tight mb-4 drop-shadow-sm text-white">
                    Digital eForm <span class="opacity-90">Application</span>
                </h2>
                <p class="text-lg md:text-xl text-white/90 font-medium leading-relaxed max-w-2xl mx-auto">
                    Fast. Secure. Paperless.
                </p>
            </div>

            <!-- Row 2: Content Grid -->
            <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-20 items-start">

                <!-- Col 1: QR Card (Left) -->
                <div class="flex justify-center md:justify-end">
                    <div class="relative group w-full max-w-[380px]">
                        <div
                            class="relative bg-white rounded-[2.5rem] p-8 shadow-2xl text-center transform transition hover:scale-[1.02] duration-500">

                            <!-- QR Area: Tightly wrapped for perfect centering -->
                            <div class="relative mb-6 mx-auto w-[220px] h-[220px]">
                                @if($qrContent)
                                    <div id="qrcode"
                                        class="w-full h-full bg-white flex items-center justify-center overflow-hidden">
                                    </div>
                                    <script type="text/javascript">
                                        new QRCode(document.getElementById("qrcode"), {
                                            text: "{!! $qrContent !!}",
                                            width: 220,
                                            height: 220,
                                            colorDark: "#1e293b",
                                            colorLight: "#ffffff",
                                            correctLevel: QRCode.CorrectLevel.H
                                        });
                                        // Force image display block to avoid line-height spacing issues
                                        setTimeout(() => {
                                            let qrImg = document.querySelector("#qrcode img");
                                            if (qrImg) qrImg.style.display = "block";
                                        }, 100);
                                    </script>
                                @else
                                    <div
                                        class="w-full h-full bg-gray-50 rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-gray-200">
                                        <i class='bx bx-loader-alt animate-spin text-4xl mb-3'
                                            style="color: {{ $primaryColor }}"></i>
                                        <p class="text-sm font-bold text-gray-400">Please Wait...</p>
                                    </div>
                                @endif

                                <!-- Center Logo (Bank Icon) -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg border-4 border-white z-10">
                                        <i class='bx bxs-bank text-2xl' style="color: {{ $primaryColor }}"></i>
                                    </div>
                                </div>
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
                        <!-- Step 1 -->
                        <div
                            class="flex items-center gap-5 p-5 rounded-2xl bg-white/10 border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-sm backdrop-blur-md group">
                            <div class="flex-none w-12 h-12 rounded-full bg-white text-xl font-bold flex items-center justify-center shadow-md group-hover:scale-110 transition-transform"
                                style="color: {{ $primaryColor }}">1</div>
                            <div class="text-left">
                                <h4 class="font-bold text-xl leading-none mb-1 text-white">Open Camera</h4>
                                <p class="text-white/80 text-sm font-medium">Open your phone's camera app</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div
                            class="flex items-center gap-5 p-5 rounded-2xl bg-white/10 border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-sm backdrop-blur-md group">
                            <div class="flex-none w-12 h-12 rounded-full bg-white text-xl font-bold flex items-center justify-center shadow-md group-hover:scale-110 transition-transform"
                                style="color: {{ $primaryColor }}">2</div>
                            <div class="text-left">
                                <h4 class="font-bold text-xl leading-none mb-1 text-white">Scan QR Code</h4>
                                <p class="text-white/80 text-sm font-medium">Point your camera at the code</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div
                            class="flex items-center gap-5 p-5 rounded-2xl bg-white/10 border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-sm backdrop-blur-md group">
                            <div class="flex-none w-12 h-12 rounded-full bg-white text-xl font-bold flex items-center justify-center shadow-md group-hover:scale-110 transition-transform"
                                style="color: {{ $primaryColor }}">3</div>
                            <div class="text-left">
                                <h4 class="font-bold text-xl leading-none mb-1 text-white">Apply Securely</h4>
                                <p class="text-white/80 text-sm font-medium">Fill out the form on your device</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="flex-none py-8 text-center">
            <p class="text-white/60 text-xs font-bold tracking-[0.2em] uppercase">&copy; {{ date('Y') }} Bank Muamalat
                Malaysia Berhad</p>
        </div>
    </div>
</body>

</html>
```