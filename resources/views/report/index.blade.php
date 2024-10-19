<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report') }}
        </h2>
    </x-slot>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div x-data="{ showFilter: false }" class="p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Transaction Report</h3>
                <div class='flex items-right justify-end space-x-4'>
                    <!-- Show/Hide Filter Button -->
                    <button @click="showFilter = !showFilter"
                            x-text="showFilter ? 'Hide Filter' : 'Show Filter'"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-150 ease-in-out">
                    </button>
                </div>

                <form id="filter-form" method="GET" action="{{ route('report.index') }}">
                    <div x-show="showFilter" class="mt-4">
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <input type="text" id="date-range" placeholder="Select Date Range" name="date_range" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            </div>

                            <div class="relative">
                                <select name="type" class="w-full bg-white dark:bg-gray-800 placeholder:text-slate-400 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-300 ease-in-out focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer">
                                    <option value="" disabled selected>All Type</option>
                                    <option value="type1">Type 1</option>
                                    <option value="type2">Type 2</option>
                                </select>
                            </div>

                            <div class="relative">
                                <select name="status" class="w-full bg-white dark:bg-gray-800 placeholder:text-slate-400 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-300 ease-in-out focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer">
                                    <option value="" disabled selected>All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>

                            <div class="relative">
                                <select name="currency" class="w-full bg-white dark:bg-gray-800 placeholder:text-slate-400 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-300 ease-in-out focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer">
                                    <option value="" disabled selected>All Currency</option>
                                    <option value="usd">USD</option>
                                    <option value="eur">EUR</option>
                                </select>
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-150 ease-in-out">
                                Apply Filter
                            </button>
                            <button type="button" @click="showFilter = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md shadow hover:bg-gray-400 transition duration-150 ease-in-out">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference NO</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Receiver</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Merchant</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Add your transaction rows here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('#date-range').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month')
            });
        });
    </script>
</x-app-layout>
