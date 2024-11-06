<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Service\merchantCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

use function Laravel\Prompts\select;

class ReportController extends Controller
{
    public $merchantCodeService;
    public function __construct(merchantCodeService $merchantCodeService)
    {
        $this->merchantCodeService = $merchantCodeService;
    }
    public function index(Request $request)
    {

        $merchant = $this->merchantCodeService->getMerchantCode();

    

        if (!$merchant || empty($merchant)) {
            $title = 'Merchant not found';
            $message = 'Please contact somxchange techical sopport team to resolve this issue ' . env('TechicalSopportNumber');
            return view('report.404', compact('title', 'message'));
        }
        $totalNumberofTransactions = $this->FetchMerchantTransaction($request)->where('merchant_id', $merchant->id)->count();
        $totalVatCharges = $this->FetchMerchantTransaction($request)
            ->where('merchant_id', $merchant->id)->sum('amount');
        if ($request->filled('export') && $request->export == 1) {
            return $this->exportToCsv($request);
        }
        return view('report.index', [
            'transactions' => $this->FetchMerchantTransaction($request)->where('merchant_id', $merchant->id)->paginate(10),
            'filteredTransactionCount' => $totalNumberofTransactions,
            'filteredVatTotal' => $totalVatCharges,
            'currencies' => $this->getCurrencies(),
            'statuses' => $this->getStatus(),
            'cashiers' => $this->getMerchantUuid()
        ]);
    }

    protected function FetchMerchantTransaction($request)
    {
        $query = DB::table('merchant_payments')
            ->join('merchants', 'merchants.id', '=', 'merchant_payments.merchant_id')
            ->leftJoin('users', 'users.id', '=', 'merchant_payments.user_id')
            ->where('merchant_payments.merchant_id', $this->returnMerchantCode()->id)
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

        if ($request->filled('cashier')) {
            $query->where('merchant_payments.user_id', $request->cashier);
        }

        if ($request->filled('status')) {
            $query->where('merchant_payments.status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('merchant_payments.currency_id', $request->currency);
        }

        return $query;
    }

    protected function exportToCsv($request)
    {
        $merchant = $this->merchantCodeService->getMerchantCode();

        if (!$merchant || empty($merchant)) {
            $title = 'Merchant not found';
            $message = 'Please contact somxchange techical sopport team to resolve this issue [+252 770835017]';
            return view('report.404', compact('title', 'message'));
        }

        $transactions = $this->FetchMerchantTransaction($request)->where('merchant_id', $merchant->id)->get();
        $filename = 'transactions_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = [
            'ID',
            'Merchant',
            'Amount',
            'Status',
            'Sender',
            'VAT Charges',
            'Date'
        ];
        $callback = function () use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->uuid,
                    $transaction->merchant_name . ' - ' . $transaction->merchant_number,
                    $transaction->amount . ' ' . $this->getCurrenyName($transaction->currency_id),
                    $transaction->status,
                    $transaction->sender,
                    $transaction->vat_charges,
                    $transaction->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    function getCurrencies()
    {
        $currencyIds = DB::table('merchant_payments')
            ->distinct('currency_id')
            ->pluck('currency_id');
        return DB::table('currencies')
            ->whereIn('id', $currencyIds)->select('id', 'name')->get();
    }

    function getStatus()
    {
        return DB::table('merchant_payments')
            ->distinct('status')
            ->pluck('status');
    }

    function getMerchantUuid()
    {
        $merchant = $this->returnMerchantCode();
        $users_ids = DB::table('merchant_payments')
            ->where('merchant_id', $merchant->id)
            ->distinct()
            ->pluck('user_id');

        $usersInfo = DB::table('users')
            ->whereIn('id', $users_ids)
            ->select('id', 'first_name', 'last_name')
            ->get();

        $merchants = [];
        foreach ($usersInfo as $user) {
            $merchant = DB::table('merchants')->where('user_id', $user->id)->first();
            if ($merchant) {
                $merchants[] = [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ];
            }
        }

        return $merchants;
    }

    function getCurrenyName($id)
    {
        return DB::table('currencies')->where('id', $id)->value('name');
    }

    function returnMerchantCode()
    {
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
