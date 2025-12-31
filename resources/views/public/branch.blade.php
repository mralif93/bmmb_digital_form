@extends('layouts.public')

@section('title', $branch->branch_name . ' - BMMB Digital Forms')

@section('content')
    <!-- Branch Header Section -->
    <section class="bg-primary-600 py-10 sm:py-12 md:py-14 lg:py-16">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
            <div class="text-center">
                <div
                    class="w-20 h-20 sm:w-24 sm:h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-5 sm:mb-6 shadow-lg">
                    <i class='bx bx-building text-white text-4xl sm:text-5xl'></i>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 sm:mb-5 leading-tight px-4">
                    {{ $branch->branch_name }}
                </h1>
                <p class="text-white/90 text-base sm:text-lg md:text-xl max-w-3xl mx-auto leading-relaxed px-4">
                    {{ $branch->address }}
                </p>
                <div
                    class="mt-5 sm:mt-6 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6 text-white/90 text-sm sm:text-base px-4">
                    <span class="flex items-center gap-2"><i class='bx bx-envelope text-lg'></i> {{ $branch->email }}</span>
                    <span class="flex items-center gap-2"><i class='bx bx-map text-lg'></i> {{ $branch->state }}</span>
                </div>
            </div>
        </div>
    </section>


    <!-- Available Forms Section -->
    <section id="features"
        class="min-h-screen flex items-start bg-gray-100 dark:bg-gray-900 py-10 sm:py-12 md:py-14 lg:py-16">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 w-full">
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

                // Color classes mapping
                $colorClasses = [
                    'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'hover' => 'hover:bg-blue-200'],
                    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'hover' => 'hover:bg-green-200'],
                    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'hover' => 'hover:bg-purple-200'],
                    'orange' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'hover' => 'hover:bg-primary-200'],
                    'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'hover' => 'hover:bg-primary-200'],
                ];
            @endphp

            @if($forms->isEmpty())
                <div class="text-center py-16 sm:py-20">
                    <div
                        class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-5 sm:mb-6">
                        <i class='bx bx-file-blank text-3xl sm:text-4xl text-gray-400 dark:text-gray-500'></i>
                    </div>
                    <h4 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3">No forms available</h4>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Please check back later or contact support.
                    </p>
                </div>
            @else
                <!-- Form Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6">
                    @foreach($forms as $form)
                        @php
                            $formConfig = $formIcons[$form->slug] ?? ['icon' => $defaultIcon, 'color' => $defaultColor];
                            $icon = $formConfig['icon'];
                            $color = $formConfig['color'];
                            $colors = $colorClasses[$color] ?? $colorClasses['primary'];

                            // Build form URL with branch parameter
                            $formUrl = in_array($form->slug, ['raf', 'dar', 'dcr', 'srf'])
                                ? route('public.forms.' . $form->slug, ['branch' => $branch->ti_agent_code])
                                : route('public.forms.slug', ['slug' => $form->slug, 'branch' => $branch->ti_agent_code]);
                        @endphp
                        <div
                            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-7 border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col h-full">
                            <div
                                class="absolute top-0 right-0 w-24 h-24 sm:w-28 sm:h-28 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                            </div>
                            <div class="relative flex flex-col h-full">
                                <div
                                    class="w-14 h-14 sm:w-16 sm:h-16 {{ $colors['bg'] }} dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mb-4 sm:mb-5 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-100/20 dark:shadow-primary-900/20">
                                    <i class='bx {{ $icon }} {{ $colors['text'] }} dark:text-primary-400 text-2xl sm:text-3xl'></i>
                                </div>
                                <h3
                                    class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $form->name }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-5 leading-relaxed flex-grow text-sm sm:text-base">
                                    {{ $form->description ?? 'Complete this form to submit your request.' }}
                                </p>
                                <a href="{{ $formUrl }}"
                                    class="block w-full {{ $colors['bg'] }} {{ $colors['hover'] }} {{ $colors['text'] }} text-center px-5 py-3 sm:py-3.5 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-sm sm:text-base shadow-md hover:shadow-lg">
                                    <span>Start Form</span>
                                    <i
                                        class='bx bx-right-arrow-alt ml-2 text-lg sm:text-xl group-hover:translate-x-1 transition-transform'></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection