<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Error') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Error Card -->
            <div class="flex justify-center">
                <div class="bg-gray-100 dark:bg-gray-900 p-8 rounded-lg shadow-lg transform hover:scale-105 transition duration-300 max-w-lg w-full">
                    <!-- Error Icon and Title -->
                    <div class="flex flex-col items-center">
                        <!-- Larger, centered error icon -->
                        <svg class="w-20 h-20 text-red-600 dark:text-red-400 mb-6" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 3.5a.75.75 0 01.75.75v4.5a.75.75 0 11-1.5 0v-4.5A.75.75 0 0110 5.5zm.75 8a.75.75 0 11-1.5 0v-.5a.75.75 0 111.5 0v.5z"/>
                        </svg>

                        <!-- Error Title -->
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                            {{ $title ?? 'Page Not Found' }}
                        </h3>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 mb-4 text-center">
                            {{ $message ?? 'Sorry, the page you are looking for could not be found.' }}
                        </p>
                        <p class="text-center p-4">
                            <a href="/" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                                Go back to Home
                            </a>
                        </p>
                    </div>

                    <!-- Action Button -->

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
