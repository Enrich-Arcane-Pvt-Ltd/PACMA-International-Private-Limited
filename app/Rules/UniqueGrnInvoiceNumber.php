<?php

namespace App\Rules;

use App\Grn;
use Illuminate\Contracts\Validation\Rule;

class UniqueGrnInvoiceNumber implements Rule
{
    private $ignoreId;
    private $number = '';

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        if (!is_string($value)) return false;
        $this->number = trim($value);
        $query = Grn::whereRaw('LOWER(TRIM(supplier_invoice_number)) = LOWER(?)', [$this->number]);
        if ($this->ignoreId !== null) $query->where('id', '!=', $this->ignoreId);
        return !$query->exists();
    }

    public function message()
    {
        return __('Supplier Invoice Number :number already exists. Do not place another GRN with this number. Open the existing GRN and use Add Item.', ['number' => e($this->number)]);
    }
}
