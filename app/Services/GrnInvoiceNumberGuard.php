<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GrnInvoiceNumberGuard
{
    public static function run(Request $request, callable $callback)
    {
        $number = $request->input('supplier_invoice_number');
        if (!is_string($number)) return $callback();
        $number = trim($number);
        $request->merge(['supplier_invoice_number' => $number]);
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'mysql' || $number === '') return $callback();
        // Serialize create/edit requests for the same number without a schema change.
        $key = 'grn-inv:' . sha1($connection->getDatabaseName() . ':' . mb_strtolower($number));
        $result = $connection->selectOne('SELECT GET_LOCK(?, 10) AS acquired', [$key]);
        if (!$result || (int) $result->acquired !== 1) {
            throw ValidationException::withMessages(['supplier_invoice_number' => __('This invoice number is being saved. Please retry shortly.')]);
        }
        try {
            return $callback();
        } finally {
            $connection->selectOne('SELECT RELEASE_LOCK(?) AS released', [$key]);
        }
    }
}
