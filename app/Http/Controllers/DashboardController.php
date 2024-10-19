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
        $vatRevenue = $this->calculateVatRevenue();
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
            ->join('merchants', 'merchants.id', '=', 'merchant_payments.merchant_id')
            ->leftJoin('users', 'users.id', '=', 'merchant_payments.user_id')
            ->select(
                'merchant_payments.*',
                'merchants.business_name as merchant_name',
                'merchants.merchant_uuid as merchant_number',
                DB::raw('
                        CASE
                            WHEN users.formattedPhone IS NOT NULL
                            THEN CONCAT(users.formattedPhone)
                            ELSE merchant_payments.reference_number
                        END as sender
                    ')

            )
            ->where('merchant_payments.vat_charges', '>', 0,)
            ->latest('merchant_payments.created_at')

            ->take(10)
            ->get();
    }


    protected function getAllMerchantPaymentsCount()
    {
        return DB::table('merchant_payments')
            ->where('vat_charges', '>', '0')
            ->count();
    }

    protected function calculateVatRevenue()
    {
        // Calculate VAT based on the count of merchant payments.
        return DB::table('merchant_payments')->where('vat_charges', '>', '0')->whereDate('created_at', '>=', now()->toDateString())->sum('vat_charges');
    }

    protected function getNumberOfMerchants()
    {
        // Directly count the number of merchants.
        return DB::table('merchants')
            ->where('vat', '>', '0')
            ->distinct('user_id')
            ->count('user_id');
    }
}
