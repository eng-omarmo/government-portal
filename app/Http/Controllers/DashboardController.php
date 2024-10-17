<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $merchantPaymentCount = $this->getAllMerchantPaymentsCount();
        $recentTransactions = $this->recentTransactions();
        $vatRevenue = $this->calculateVatRevenue($merchantPaymentCount);
        $numberOfMerchants = $this->getNumberOfMerchants();

        return view('dashboard', [
            'merchantPaymentCount' => $merchantPaymentCount,
            'recentTransactions' => $recentTransactions,
            'vatRevenue' => $vatRevenue,
            'numberOfMerchantCount' => $numberOfMerchants
        ]);
    }

    protected function recentTransactions()
    {
        return DB::table('merchant_payments')
            ->leftJoin('users', 'merchant_payments.user_id', '=', 'users.id')
            ->select('merchant_payments.*', 'users.first_name as name')
            ->latest('merchant_payments.created_at') // Specify the table explicitly
            ->take(5)
            ->get();
    }


    protected function getAllMerchantPaymentsCount()
    {
        // Get the count of all merchant payments without fetching the entire dataset.
        return DB::table('merchant_payments')->count();
    }

    protected function calculateVatRevenue($merchantPaymentCount)
    {
        // Calculate VAT based on the count of merchant payments.
        $currentVat = DB::table('vats')->value('amount'); // Using 'value' to fetch a single column.
        return $merchantPaymentCount * $currentVat;
    }

    protected function getNumberOfMerchants()
    {
        // Directly count the number of merchants.
        return DB::table('users')->where('type', 'merchant')->count();
    }
}
