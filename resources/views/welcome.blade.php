@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Welcome')

@section('content')
<!-- Available Forms Section -->
<section id="features" class="min-h-screen flex items-center bg-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-16">
        <!-- Section Header -->
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                Digital Forms
            </h2>
            <p class="text-sm text-gray-600 max-w-3xl mx-auto">
                Secure, efficient, and compliant digital forms designed to meet your specific needs
            </p>
        </div>
        
        <!-- Form Cards Grid -->
        @php
            // Get all active public forms
            $forms = \App\Models\Form::where('status', 'active')
                ->where('is_public', true)
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
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
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
                            'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'hover' => 'hover:bg-orange-200'],
                            'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'hover' => 'hover:bg-primary-200'],
                        ];
                        $colors = $colorClasses[$color] ?? $colorClasses['primary'];
                    @endphp
                    <div class="group relative bg-white rounded-xl p-5 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 flex flex-col h-full">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative flex flex-col h-full">
                            <div class="w-12 h-12 {{ $colors['bg'] }} rounded-full flex items-center justify-center mb-3 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-md">
                                <i class='bx {{ $icon }} {{ $colors['text'] }} text-xl'></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                                {{ $form->name }}
                            </h3>
                            <p class="text-gray-600 mb-3 leading-relaxed flex-grow text-xs">
                                {{ $form->description ?? 'Complete this form to submit your request.' }}
                            </p>
                            <a href="{{ in_array($form->slug, ['raf', 'dar', 'dcr', 'srf']) ? route('public.forms.' . $form->slug) : route('public.forms.slug', $form->slug) }}" class="block w-full {{ $colors['bg'] }} {{ $colors['hover'] }} {{ $colors['text'] }} text-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-xs">
                                <span>Start Form</span>
                                <i class='bx bx-right-arrow-alt ml-2 text-base group-hover:translate-x-1 transition-transform'></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection

