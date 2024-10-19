<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function index()
    {
        return view('report.index', [
            'transactions' => $this->FetchMerchantTransaction(request()),
        ]);
    }
    function FetchMerchantTransaction($request)
    {
        $status = $request['status'] ?? '';
        $currency = $request['currency'] ?? '';
        $transacrtionType = $request['type'] ?? '';
        $data = [];

        if ($status !== '' || $currency !== '' || $transacrtionType !== '') {
            $data = DB::table('merchant_payments')
                ->join('merchants', 'merchants.id', '=', 'merchant_payments.merchant_id')
                ->leftJoin('users', 'users.id', '=', 'merchants.user_id')
                ->select(
                    'merchant_payments.*',
                    'merchants.business_name as merchant_name,merchants.merchant_uuid as id',
                    DB::raw('
                    CASE
                        WHEN users.first_name IS NOT NULL
                        THEN CONCAT(users.first_name, " ", users.last_name)
                        ELSE merchant_payments.reference_number
                    END as name
                ')
                )
                ->where('merchant_payments.status', $status)
                ->where('merchant_payments.currency', $currency)
                ->where('merchant_payments.type', $transacrtionType)
                ->latest('merchant_payments.created_at')
                ->get();
        }
        return $data;
    }
}
