<?php

namespace App\Http\Controllers\Service;

use Illuminate\Support\Facades\DB;

class merchantCodeService
{
    public function getMerchantCode()
    {
        $merchantCode = env('merchant_code');
        return  DB::table('merchants')->where('merchant_uuid', $merchantCode)->first();
    }
}
