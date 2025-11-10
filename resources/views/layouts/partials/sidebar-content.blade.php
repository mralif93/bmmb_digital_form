<nav class="p-4 space-y-1">
    <div class="mb-8">
        <div class="space-y-0.5">
            <a href="{{ route('admin.dashboard') }}" @click="if (window.innerWidth < 1024) { setTimeout(() => { window.dispatchEvent(new CustomEvent('close-sidebar')); }, 100); }" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                <i class='bx bx-home-alt-2 mr-3 text-base'></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" @click="if (window.innerWidth < 1024) { setTimeout(() => { const event = new CustomEvent('close-sidebar'); window.dispatchEvent(event); }, 100); }" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class='bx bx-user mr-3 text-base'></i>
                <span class="font-medium">Users</span>
            </a>
            
            <a href="{{ route('admin.branches.index') }}" @click="if (window.innerWidth < 1024) { setTimeout(() => { const event = new CustomEvent('close-sidebar'); window.dispatchEvent(event); }, 100); }" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('admin.branches*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                <i class='bx bx-building mr-3 text-base'></i>
                <span class="font-medium">Branches</span>
            </a>
            
            <a href="{{ route('admin.qr-codes.index') }}" @click="if (window.innerWidth < 1024) { setTimeout(() => { const event = new CustomEvent('close-sidebar'); window.dispatchEvent(event); }, 100); }" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('admin.qr-codes*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                <i class='bx bx-qr-scan mr-3 text-base'></i>
                <span class="font-medium">QR Codes</span>
            </a>
            
            <!-- Forms Section -->
            <div x-data="{ open: {{ request()->routeIs('admin.forms.*') || request()->routeIs('admin.form-builder.*') || request()->routeIs('admin.form-sections.*') || request()->routeIs('admin.submissions.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('admin.forms.*') || request()->routeIs('admin.form-builder.*') || request()->routeIs('admin.form-sections.*') || request()->routeIs('admin.submissions.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                    <div class="flex items-center">
                        <i class='bx bx-file-blank mr-3 text-base'></i>
                        <span class="font-medium">Forms</span>
                    </div>
                    <i class='bx bx-chevron-right text-xs transition-transform' :class="{ 'rotate-90': open }"></i>
                </button>
            <div x-show="open" x-transition class="mt-1 space-y-0.5">
                <!-- Forms -->
                <a href="{{ route('admin.forms.index') }}" class="flex items-center px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.forms.*') && !request()->routeIs('admin.form-builder.*') && !request()->routeIs('admin.form-sections.*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                    <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                    <span>Form</span>
                </a>
                
                <!-- Form Sections -->
                <div x-data="{ openSections: {{ request()->routeIs('admin.form-sections.*') ? 'true' : 'false' }} }">
                    <button @click="openSections = !openSections" class="w-full flex items-center justify-between px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-sections.*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        <div class="flex items-center">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            <span>Form Sections</span>
                        </div>
                        <i class='bx bx-chevron-right text-xs transition-transform' :class="{ 'rotate-90': openSections }"></i>
                    </button>
                    <div x-show="openSections" x-transition class="ml-4 mt-1 space-y-0.5">
                        @php
                            $forms = \App\Models\Form::whereIn('slug', ['raf', 'dar', 'dcr', 'srf'])->get()->keyBy('slug');
                        @endphp
                        @if($forms->has('raf'))
                            <a href="{{ route('admin.form-sections.index', $forms->get('raf')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-sections.*') && request()->route('form') && request()->route('form')->slug == 'raf' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                RAF Sections
                            </a>
                        @endif
                        @if($forms->has('dar'))
                            <a href="{{ route('admin.form-sections.index', $forms->get('dar')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-sections.*') && request()->route('form') && request()->route('form')->slug == 'dar' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                DAR Sections
                            </a>
                        @endif
                        @if($forms->has('dcr'))
                            <a href="{{ route('admin.form-sections.index', $forms->get('dcr')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-sections.*') && request()->route('form') && request()->route('form')->slug == 'dcr' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                DCR Sections
                            </a>
                        @endif
                        @if($forms->has('srf'))
                            <a href="{{ route('admin.form-sections.index', $forms->get('srf')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-sections.*') && request()->route('form') && request()->route('form')->slug == 'srf' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                SRF Sections
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Form Builders -->
                <div x-data="{ openBuilders: {{ request()->routeIs('admin.form-builder.*') ? 'true' : 'false' }} }">
                    <button @click="openBuilders = !openBuilders" class="w-full flex items-center justify-between px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-builder.*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        <div class="flex items-center">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            <span>Form Builders</span>
                        </div>
                        <i class='bx bx-chevron-right text-xs transition-transform' :class="{ 'rotate-90': openBuilders }"></i>
                    </button>
                    <div x-show="openBuilders" x-transition class="ml-4 mt-1 space-y-0.5">
                        @php
                            $forms = \App\Models\Form::whereIn('slug', ['raf', 'dar', 'dcr', 'srf'])->get()->keyBy('slug');
                        @endphp
                        
                        @if($forms->has('raf'))
                            <a href="{{ route('admin.form-builder.index', $forms->get('raf')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-builder.*') && request()->route('form') && request()->route('form')->slug == 'raf' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                RAF Builder
                            </a>
                        @endif
                        
                        @if($forms->has('dar'))
                            <a href="{{ route('admin.form-builder.index', $forms->get('dar')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-builder.*') && request()->route('form') && request()->route('form')->slug == 'dar' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                DAR Builder
                            </a>
                        @endif
                        
                        @if($forms->has('dcr'))
                            <a href="{{ route('admin.form-builder.index', $forms->get('dcr')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-builder.*') && request()->route('form') && request()->route('form')->slug == 'dcr' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                DCR Builder
                            </a>
                        @endif
                        
                        @if($forms->has('srf'))
                            <a href="{{ route('admin.form-builder.index', $forms->get('srf')) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.form-builder.*') && request()->route('form') && request()->route('form')->slug == 'srf' ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                                SRF Builder
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Form Submissions -->
                <div x-data="{ openSubmissions: {{ request()->routeIs('admin.submissions.*') ? 'true' : 'false' }} }">
                    <button @click="openSubmissions = !openSubmissions" class="w-full flex items-center justify-between px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.submissions.*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        <div class="flex items-center">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            <span>Form Submission</span>
                        </div>
                        <i class='bx bx-chevron-right text-xs transition-transform' :class="{ 'rotate-90': openSubmissions }"></i>
                    </button>
                    <div x-show="openSubmissions" x-transition class="ml-4 mt-1 space-y-0.5">
                        <a href="{{ route('admin.submissions.raf') }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.submissions.raf*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            RAF Submissions
                        </a>
                        <a href="{{ route('admin.submissions.dar') }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.submissions.dar*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            DAR Submissions
                        </a>
                        <a href="{{ route('admin.submissions.dcr') }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.submissions.dcr*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            DCR Submissions
                        </a>
                        <a href="{{ route('admin.submissions.srf') }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 dark:text-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.submissions.srf*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                            <i class='bx bx-right-arrow-alt mr-2 text-xs'></i>
                            SRF Submissions
                        </a>
                    </div>
                </div>
            </div>
            </div>
            
            <a href="{{ route('admin.audit-trails.index') }}" @click="if (window.innerWidth < 1024) { setTimeout(() => { const event = new CustomEvent('close-sidebar'); window.dispatchEvent(event); }, 100); }" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('admin.audit-trails*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                <i class='bx bx-history mr-3 text-base'></i>
                <span class="font-medium">Audit Trail</span>
            </a>
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="space-y-0.5">
                        <a href="{{ route('admin.settings') }}" @click="if (window.innerWidth < 1024) { setTimeout(() => { window.dispatchEvent(new CustomEvent('close-sidebar')); }, 100); }" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-400 transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}">
                <i class='bx bx-cog mr-3 text-base'></i>
                <span class="font-medium">Settings</span>
            </a>
        </div>
    </div>
</nav>

