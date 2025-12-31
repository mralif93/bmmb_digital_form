@extends('layouts.admin-minimal')

@section('title', 'Audit Trail Details - BMMB Digital Forms')
@section('page-title', 'Audit Trail Details')
@section('page-description', 'View detailed information about this action')

@section('content')
    <div class="mb-4 flex items-center justify-end">
        <a href="{{ route('admin.audit-trails.index') }}"
            class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            <i class='bx bx-arrow-back mr-1.5'></i>
            Back to Audit Trail
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-info-circle mr-2 text-orange-600 dark:text-orange-400'></i>
                Basic Information
            </h3>
            <div class="space-y-3">
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">Action</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'update' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'delete' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'login' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                'logout' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
                            ];
                            $color = $actionColors[$auditTrail->action] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $color }}">
                            {{ $auditTrail->action_display }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">Description</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        {{ $auditTrail->description ?? 'N/A' }}</dd>
                </div>
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">User</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        {{ $auditTrail->user ? $auditTrail->user->full_name . ' (' . $auditTrail->user->email . ')' : 'System' }}
                    </dd>
                </div>
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">Date & Time</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        {{ $timezoneHelper->convert($auditTrail->created_at)?->format($dateFormat . ' ' . $timeFormat) }}
                    </dd>
                </div>
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">Model Type</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        {{ $auditTrail->model_type ? class_basename($auditTrail->model_type) : 'N/A' }}
                    </dd>
                </div>
                <div class="flex items-start justify-between pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">Model ID</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        {{ $auditTrail->model_id ?? 'N/A' }}
                    </dd>
                </div>
            </div>
        </div>

        <!-- Request Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-globe mr-2 text-orange-600 dark:text-orange-400'></i>
                Request Information
            </h3>
            <div class="space-y-3">
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">IP Address</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">
                        {{ $auditTrail->ip_address ?? 'N/A' }}</dd>
                </div>
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">User Agent</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1 break-all">
                        {{ $auditTrail->user_agent ?? 'N/A' }}</dd>
                </div>
                <div class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">URL</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1 break-all">
                        {{ $auditTrail->url ?? 'N/A' }}</dd>
                </div>
                <div class="flex items-start justify-between pb-2">
                    <dt class="text-xs font-semibold text-gray-700 dark:text-gray-300 pr-4">HTTP Method</dt>
                    <dd class="text-xs text-gray-900 dark:text-white text-right flex-1">{{ $auditTrail->method ?? 'N/A' }}
                    </dd>
                </div>
            </div>
        </div>

        <!-- Changes -->
        @if($auditTrail->old_values || $auditTrail->new_values)
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class='bx bx-edit mr-2 text-orange-600 dark:text-orange-400'></i>
                    Changes
                </h3>

                @php
                    $oldValues = $auditTrail->old_values ?? [];
                    $newValues = $auditTrail->new_values ?? [];
                    // Get all unique keys and ensure no duplicates
                    $allKeys = array_values(array_unique(array_merge(array_keys($oldValues), array_keys($newValues))));

                    // Helper function to format datetime values consistently
                    $formatValue = function ($value) use ($timezoneHelper, $dateFormat, $timeFormat) {
                        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}/', $value)) {
                            try {
                                $date = \Carbon\Carbon::parse($value);
                                $convertedDate = $timezoneHelper->convert($date);
                                return $convertedDate ? $convertedDate->format($dateFormat . ' ' . $timeFormat) : $value;
                            } catch (\Exception $e) {
                                return $value;
                            }
                        }
                        return $value;
                    };
                @endphp

                @if(count($allKeys) > 0)
                    <!-- Comparison Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Field</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Old Value</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        New Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($allKeys as $key)
                                    @php
                                        $oldValue = $oldValues[$key] ?? null;
                                        $newValue = $newValues[$key] ?? null;

                                        // Format datetime values consistently
                                        $oldValue = $formatValue($oldValue);
                                        $newValue = $formatValue($newValue);

                                        $hasChanged = $oldValue !== $newValue;
                                        $isNew = !isset($oldValues[$key]) && isset($newValues[$key]);
                                        $isDeleted = isset($oldValues[$key]) && !isset($newValues[$key]);

                                        // Skip if values are the same (unless it's a new or deleted field)
                                        if (!$hasChanged && !$isNew && !$isDeleted) {
                                            continue;
                                        }
                                    @endphp
                                    <tr class="{{ $hasChanged ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                                {{ ucwords(str_replace('_', ' ', $key)) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($isNew)
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">—</span>
                                            @elseif($oldValue === null)
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">null</span>
                                            @elseif(is_array($oldValue) || is_object($oldValue))
                                                <pre
                                                    class="text-xs bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-2 rounded text-gray-900 dark:text-white overflow-x-auto max-w-xs">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <span
                                                    class="text-xs text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded inline-block">{{ is_bool($oldValue) ? ($oldValue ? 'true' : 'false') : (string) $oldValue }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($isDeleted)
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">—</span>
                                            @elseif($newValue === null)
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">null</span>
                                            @elseif(is_array($newValue) || is_object($newValue))
                                                <pre
                                                    class="text-xs bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-2 rounded text-gray-900 dark:text-white overflow-x-auto max-w-xs">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <span
                                                    class="text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded inline-block">{{ is_bool($newValue) ? ($newValue ? 'true' : 'false') : (string) $newValue }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Fallback to JSON view if structure is complex -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($auditTrail->old_values)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <i class='bx bx-minus-circle mr-1 text-red-600 dark:text-red-400'></i>
                                    Old Values
                                </h4>
                                <pre
                                    class="text-xs bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 rounded-lg overflow-x-auto text-gray-900 dark:text-white">{{ json_encode($auditTrail->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                        @if($auditTrail->new_values)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <i class='bx bx-plus-circle mr-1 text-green-600 dark:text-green-400'></i>
                                    New Values
                                </h4>
                                <pre
                                    class="text-xs bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 rounded-lg overflow-x-auto text-gray-900 dark:text-white">{{ json_encode($auditTrail->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- Request Data -->
        @if($auditTrail->request_data)
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class='bx bx-data mr-2 text-orange-600 dark:text-orange-400'></i>
                    Request Data
                </h3>
                <pre
                    class="text-xs bg-gray-100 dark:bg-gray-700 p-3 rounded-lg overflow-x-auto text-gray-900 dark:text-white">{{ json_encode($auditTrail->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif
    </div>
@endsection