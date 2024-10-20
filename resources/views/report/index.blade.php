<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div x-data="{ showFilter: false }" class="p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Transaction Report</h3>

                <div class='flex justify-between items-center mb-4'>
                    <!-- Show/Hide Filter Button -->
                    <button @click="showFilter = !showFilter"
                            x-text="showFilter ? 'Hide Filter' : 'Show Filter'"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-150 ease-in-out">
                    </button>

                    <form id="filter-form" method="GET" action="{{ route('report.index') }}" class="flex items-center space-x-4">
                        <div x-show="showFilter" class="flex items-center space-x-4">
                            <div class="relative">
                                <input type="text" id="date-range" placeholder="Select Date Range" name="date_range"
                                       class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            </div>

                            <div class="relative">
                                <select name="status" class="w-full bg-white dark:bg-gray-800 placeholder:text-slate-400 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-300 ease-in-out focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <select name="currency" class="w-full bg-white dark:bg-gray-800 placeholder:text-slate-400 text-slate-700 dark:text-gray-200 text-sm border border-slate-300 dark:border-gray-600 rounded pl-3 pr-8 py-2 transition duration-300 ease-in-out focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer">
                                    <option value="">All Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition duration-150 ease-in-out">
                                Apply Filter
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
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Vat Charges</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->created_at }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->sender }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->merchant_name .' - '.$transaction->merchant_number  }}</td>
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
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 flex justify-center mt-5">No transactions found.</h2>
                        </div>
                    @endif
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
