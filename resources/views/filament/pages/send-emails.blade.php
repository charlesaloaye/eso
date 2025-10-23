<x-filament-panels::page>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Send & Schedule Emails</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Compose and schedule emails to your users with
                        custom templates</p>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Form Header -->
            <div
                class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Email Configuration</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Configure your email settings and recipients</p>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                {{ $this->form }}
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    @foreach ($this->getActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Tips Card -->
        <div
            class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-blue-800 dark:text-blue-200 mb-3">Quick Tips</h3>
                    <ul class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                        <li class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                            <span>Select a template to see available variables</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                            <span>Use "All Enrollments" to send to everyone</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                            <span>Schedule emails for future delivery</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2"></div>
                            <span>Variables like <code
                                    class="bg-blue-100 dark:bg-blue-900/30 px-1 py-0.5 rounded text-xs">{!! '{{ ' !!}name{!! ' }}' !!}</code>
                                are replaced automatically</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
