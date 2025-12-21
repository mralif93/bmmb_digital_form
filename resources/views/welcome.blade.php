@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Welcome')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-primary-600 dark:bg-primary-700 py-12 md:py-16 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0"
                style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;">
            </div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Badge -->
            <div
                class="inline-flex items-center mb-4 px-4 py-2 bg-white/20 dark:bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-white border border-white/30 shadow-lg">
                <i class='bx bx-shield-check mr-2'></i>
                <span>Secure Digital Forms Platform</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                Welcome to BMMB
                <span class="block text-white/90 mt-1">Digital Forms</span>
            </h1>

            <!-- Subheading -->
            <p class="text-base md:text-lg text-white/90 max-w-2xl mx-auto mb-6 leading-relaxed font-light">
                Streamline your requests and transactions with our comprehensive suite of professional digital forms
            </p>
        </div>
    </section>

    <!-- Available Forms Section -->
    <section id="features" class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <!-- Section Header -->
            <div class="text-center mb-10 md:mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                    Available Forms
                </h2>
                <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Choose the form that best fits your needs and get started in minutes
                </p>
            </div>

            <!-- Form Cards Grid -->
            @php
                // Get all active public forms
                $forms = \App\Models\Form::where('status', 'active')
                    ->where('is_public', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();

                // Icon and color mapping for known forms
                $formIcons = [
                    'raf' => ['icon' => 'bx-money', 'color' => 'primary'],
                    'dar' => ['icon' => 'bx-data', 'color' => 'primary'],
                    'dcr' => ['icon' => 'bx-edit', 'color' => 'primary'],
                    'srf' => ['icon' => 'bx-cog', 'color' => 'primary'],
                ];

                // Default icon and color for custom forms
                $defaultIcon = 'bx-file-blank';
                $defaultColor = 'primary';
            @endphp

            @if($forms->isEmpty())
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class='bx bx-file-blank text-2xl text-gray-400 dark:text-gray-500'></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No forms available</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Please check back later or contact support.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($forms as $form)
                        @php
                            $formConfig = $formIcons[$form->slug] ?? ['icon' => $defaultIcon, 'color' => $defaultColor];
                            $icon = $formConfig['icon'];
                            $color = $formConfig['color'];

                            // Color classes mapping
                            $colorClasses = [
                                'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'hover' => 'hover:bg-blue-200'],
                                'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'hover' => 'hover:bg-green-200'],
                                'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'hover' => 'hover:bg-purple-200'],
                                'orange' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'hover' => 'hover:bg-primary-200'],
                                'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'hover' => 'hover:bg-primary-200'],
                            ];
                            $colors = $colorClasses[$color] ?? $colorClasses['primary'];
                        @endphp
                        <div
                            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col h-full overflow-hidden">
                            <!-- Decorative Element -->
                            <div
                                class="absolute top-0 right-0 w-24 h-24 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                            </div>

                            <div class="relative flex flex-col h-full">
                                <!-- Icon -->
                                <div
                                    class="w-14 h-14 bg-primary-500 dark:bg-primary-600 rounded-xl flex items-center justify-center mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/30 dark:shadow-primary-900/30">
                                    <i class='bx {{ $icon }} text-white text-2xl'></i>
                                </div>

                                <!-- Content -->
                                <h3
                                    class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $form->name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed flex-grow">
                                    {{ Str::limit($form->description ?? 'Complete this form to submit your request quickly and securely.', 60) }}
                                </p>

                                <!-- Button -->
                                <a href="{{ in_array($form->slug, ['raf', 'dar', 'dcr', 'srf']) ? route('public.forms.' . $form->slug) : route('public.forms.slug', $form->slug) }}"
                                    class="block w-full bg-primary-500 dark:bg-primary-600 hover:bg-primary-600 dark:hover:bg-primary-700 text-white text-center px-4 py-3 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto shadow-md hover:shadow-lg text-sm">
                                    <span>Start Form</span>
                                    <i
                                        class='bx bx-right-arrow-alt ml-2 text-lg group-hover:translate-x-1 transition-transform'></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection