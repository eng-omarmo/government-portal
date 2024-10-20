<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {

        return view('report.index', [
            'transactions' => $this->FetchMerchantTransaction($request),
            'currencies' => $this->getCurrencies(),
            'statuses' => $this->getStatus(),
        ]);
    }

    protected function FetchMerchantTransaction($request)
    {
        $query = DB::table('merchant_payments')
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
            );


        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('merchant_payments.created_at', [$dates[0], $dates[1]]);
        }

        if ($request->filled('status')) {
            $query->where('merchant_payments.status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('merchant_payments.currency_id', $request->currency);
        }

        $query->where('merchant_payments.vat_charges', '>', 0);

        return $query->paginate(10);
    }
    function getCurrencies()
    {
        $currencyIds = DB::table('merchant_payments')->distinct('currency_id')->pluck('currency_id');
        return DB::table('currencies')->whereIn('id', $currencyIds)->select('id', 'name')->get();
    }
    function getStatus()
    {
        return DB::table('merchant_payments')->distinct('status')->pluck('status');
    }
}
