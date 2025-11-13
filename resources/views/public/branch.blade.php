@extends('layouts.public')

@section('title', $branch->branch_name . ' - BMMB Digital Forms')

@section('content')
<!-- Branch Header Section -->
<section class="bg-primary-600 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-building text-white text-3xl'></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
                {{ $branch->branch_name }}
            </h1>
            <p class="text-white/90 text-sm">
                {{ $branch->address }}
            </p>
            <div class="mt-3 flex items-center justify-center space-x-4 text-white/80 text-xs">
                <span><i class='bx bx-envelope mr-1'></i> {{ $branch->email }}</span>
                <span><i class='bx bx-map mr-1'></i> {{ $branch->state }}</span>
            </div>
            @if(isset($qrCodeInfo) && $qrCodeInfo['expires_at'])
            <div class="mt-4 pt-4 border-t border-white/20">
                <div class="flex flex-col items-center space-y-2 text-white/70 text-xs">
                    <span class="text-white/60">
                        Expires: {{ $qrCodeInfo['expires_at']->format('M d, Y h:i A') }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Available Forms Section -->
<section id="features" class="min-h-screen flex items-start bg-gray-100 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
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
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-file-blank text-2xl text-gray-400 dark:text-gray-500'></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No forms available</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">Please check back later or contact support.</p>
            </div>
        @else
            <!-- Form Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <div class="group relative bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 flex flex-col h-full">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative flex flex-col h-full">
                            <div class="w-12 h-12 {{ $colors['bg'] }} dark:bg-primary-900/30 rounded-full flex items-center justify-center mb-3 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-md shadow-primary-100/20 dark:shadow-primary-900/20">
                                <i class='bx {{ $icon }} {{ $colors['text'] }} dark:text-primary-400 text-xl'></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $form->name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-3 leading-relaxed flex-grow text-xs">
                                {{ $form->description ?? 'Complete this form to submit your request.' }}
                            </p>
                            <a href="{{ $formUrl }}" class="block w-full {{ $colors['bg'] }} {{ $colors['hover'] }} {{ $colors['text'] }} text-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-xs">
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

