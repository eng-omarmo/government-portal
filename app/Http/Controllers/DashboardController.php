<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Service\merchantCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public $merchantCodeService;
    public function __construct(merchantCodeService $merchantCodeService) {
      $this->merchantCodeService = $merchantCodeService;
    }
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
            ->whereDate('merchant_payments.created_at', '=', now()->toDateString())
            ->latest('merchant_payments.created_at')
            ->take(10)
            ->where('merchant_payments.merchant_id', $this->returnMerchantCode()->id)
            ->get();
    }


    protected function getAllMerchantPaymentsCount()
    {
        return DB::table('merchant_payments')
            ->where('merchant_id', $this->returnMerchantCode()->id)
            ->whereDate('created_at', '=', now()->toDateString())
            ->count();
    }


    protected function calculateVatRevenue()
    {
        return DB::table('merchant_payments')
        ->where('merchant_id', $this->returnMerchantCode()->id)
        ->whereDate('created_at', '=', now()->toDateString())
        ->sum('amount');
    }

    protected function getNumberOfMerchants()
    {
        return DB::table('merchant_payments')
            ->where('vat_charges', '>', '0')
            ->whereDate('created_at', '=', now()->toDateString())
            ->distinct('user_id')
            ->count('user_id');
    }

    function returnMerchantCode() {
        $merchantCodeService = new merchantCodeService();
        $merchant = $merchantCodeService->getMerchantCode();

        if (!$merchant || empty($merchant)) {
            $title = 'Merchant not found';
            $message = 'Please contact somxchange techical sopport team to resolve this issue ' . env('TechicalSopportNumber');
            return view('report.404', compact('title', 'message'));
        }
        return $merchant;
    }
}
