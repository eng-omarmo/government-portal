<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Summary Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-yellow-100 dark:bg-gray-800 p-6 shadow rounded-lg transition-transform transform hover:scale-105 duration-300">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-gray-100">Total Transactions</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-900 dark:text-gray-100">{{ $filteredTransactionCount }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-gray-800 p-6 shadow rounded-lg transition-transform transform hover:scale-105 duration-300">
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-gray-100">Total VAT</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-gray-100">{{ $filteredVatTotal }} USD</p>
                </div>
            </div>

            <!-- Transaction Report Section -->
            <div x-data="{ showFilter: false }" class="p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Transaction Report</h3>

                <div class="flex flex-wrap justify-between items-center mb-2">
                    <!-- Show/Hide Filter Button -->
                    <button @click="showFilter = !showFilter"
                            x-text="showFilter ? 'Hide Filter' : 'Show Filter'"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-150 ease-in-out">
                    </button>

                    <form id="filter-form" method="GET" action="{{ route('report.index') }}" class="flex items-center flex-wrap space-x-4">
                        <div x-show="showFilter" class="flex items-center flex-wrap space-x-4">
                            <div class="relative">
                                <input type="text" id="date-range" placeholder="Select Date Range" name="date_range"
                                       class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            </div>

                            <div class="relative">
                                <select name="status" class="w-full bg-white dark:bg-gray-800 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-150 focus:outline-none shadow-sm">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative w-full sm:w-64">
                                <select id="cashier-select" name="cashier" class="block w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <option value="">All Cashiers</option>
                                    @foreach($cashiers as $cashier)
                                        <option value="{{ $cashier->id }}">{{ $cashier->business_name }} - {{ $cashier->merchant_uuid }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <select name="currency" class="w-full bg-white dark:bg-gray-800 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-150 focus:outline-none shadow-sm">
                                    <option value="">All Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button onclick="filter()" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-150 ease-in-out">
                                Apply Filter
                            </button>
                            <input name="export" type="hidden" id="export" value="0">
                            <button type="button" onclick="exportCSV()" class="px-4 py-2 bg-green-500 text-white rounded-md shadow hover:bg-green-700 transition duration-150 ease-in-out">
                                Export CSV
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    @if(count($transactions) > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sender</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Receiver</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">VAT Charges</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->created_at }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->sender }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->merchant_name .' - '.$transaction->merchant_number }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->total }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->vat_charges }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div>
                            <h2 class="text-lg font-semibold text-center mt-5 text-gray-900 dark:text-gray-100">No transactions found.</h2>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Select2 and Date Range Picker CSS & JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {
            $('#cashier-select').select2({
                placeholder: 'Select a cashier',
                allowClear: true,
                width: '100%'
            });

            $('#date-range').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month')
            });
        });

        function exportCSV() {
            document.getElementById('export').value = 1;
            document.getElementById('filter-form').submit();
        }

        function filter() {
            document.getElementById('export').value = 0;
            document.getElementById('filter-form').submit();
        }
    </script>
</x-app-layout>
