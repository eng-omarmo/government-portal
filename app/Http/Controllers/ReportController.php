<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //

    public function index()
    {
        return view('report.index', [
            'transactions' => $this->FetchMerchantTransaction()
        ]);
    }
    function FetchMerchantTransaction()
    {
        $data = DB::table('merchant_payments')->get();

        return $data;
    }


}
