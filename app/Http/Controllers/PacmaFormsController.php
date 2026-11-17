<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerDetail;
use App\Invoice;
use App\Sale;
use Illuminate\Http\Request;

class PacmaFormsController extends Controller
{
    private function loadContractData($saleId = null, $preferInvoice = false)
    {
        $sale = null;
        $invoice = null;
        $customer = null;
        $customerDetail = null;
        $guarantors = collect();
        $items = collect();

        if (!empty($saleId)) {
            if ($preferInvoice) {
                $invoice = Invoice::with(['customer.details', 'sales.guarantor1.details', 'sales.guarantor2.details', 'sales.guarantor3.details', 'sales.saleItems.products', 'sales.products', 'sales.introducer', 'sales.inventory'])->find($saleId);
                if ($invoice) {
                    $sale = $invoice->sales;
                    $customer = $invoice->customer ?: optional($sale)->customer;
                    $customerDetail = optional($customer)->details;
                    $items = $sale && $sale->saleItems ? $sale->saleItems->where('status', 1) : collect();
                }
            } else {
                $sale = Sale::with(['customer.details', 'guarantor1.details', 'guarantor2.details', 'guarantor3.details', 'saleItems.products', 'products', 'introducer', 'inventory', 'invoice'])->find($saleId);
                if ($sale) {
                    $customer = $sale->customer;
                    $customerDetail = optional($customer)->details;
                    $invoice = $sale->invoice ?: Invoice::find($sale->invoice_id);
                    $items = $sale->saleItems ? $sale->saleItems->where('status', 1) : collect();
                } else {
                    $invoice = Invoice::with(['customer.details', 'sales.guarantor1.details', 'sales.guarantor2.details', 'sales.guarantor3.details', 'sales.saleItems.products', 'sales.products', 'sales.introducer', 'sales.inventory'])->find($saleId);
                    if ($invoice) {
                        $sale = $invoice->sales;
                        $customer = $invoice->customer;
                        $customerDetail = optional($customer)->details;
                        $items = $sale && $sale->saleItems ? $sale->saleItems->where('status', 1) : collect();
                    }
                }
            }

            if ($items->isEmpty() && $sale && !empty($sale->product_id)) {
                $serialNo = '';
                $grnProductId = optional(optional($sale)->inventory)->grn_product_id;
                $grnProduct = $grnProductId ? \App\GrnProduct::find($grnProductId) : null;
                if ($grnProduct && $grnProduct->productWarrantySerial && $grnProduct->productWarrantySerial->isNotEmpty()) {
                    $serials = $grnProduct->productWarrantySerial;
                    $activeSerial = $serials->firstWhere('status', 1) ?: $serials->first();
                    $serialNo = $activeSerial->serial_number ?? '';
                }
                if ($serialNo === '' && $sale->products) {
                    $serialNo = $sale->products->serial_number ?? '';
                }
                $items = collect([(object) [
                    'products' => $sale->products,
                    'quantity' => $sale->quantity ?? 1,
                    'serial_no' => $serialNo,
                    'price' => $sale->net_amount ?: ($sale->unit_price ?? 0),
                    'net_amount' => $sale->net_amount ?: ($sale->unit_price ?? 0),
                ]]);
            }

            if ($sale) {
                $guarantors = collect([$sale->guarantor1, $sale->guarantor2, $sale->guarantor3])->filter()->values();
            }
        }

        return compact('sale', 'invoice', 'customer', 'customerDetail', 'guarantors', 'items');
    }

    public function loanRequisition($id = null)
    {
        $data = $this->loadContractData($id, false);
        return view('pacma_forms.loan_requisition', $data);
    }

    public function invoiceLoanRequisition($invoiceId)
    {
        $data = $this->loadContractData($invoiceId, true);
        return view('pacma_forms.loan_requisition', $data);
    }

    public function applicationForm($id = null)
    {
        $data = $this->loadContractData($id);
        return view('pacma_forms.application_form', $data);
    }

    public function customerApplicationForm($customerId)
    {
        $customer = Customer::with('details')->findOrFail($customerId);
        $customerDetail = $customer->details;
        $sale = null;
        $invoice = null;
        $guarantors = collect();
        $items = collect();

        return view('pacma_forms.application_form', compact('sale', 'invoice', 'customer', 'customerDetail', 'guarantors', 'items'));
    }

    public function guarantorDeclaration($id = null)
    {
        $data = $this->loadContractData($id);
        return view('pacma_forms.guarantor_declaration', $data);
    }

    public function guaranteeIndemnity($id = null)
    {
        $data = $this->loadContractData($id);
        return view('pacma_forms.guarantee_indemnity', $data);
    }
}
