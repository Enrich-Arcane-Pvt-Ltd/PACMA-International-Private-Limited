<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PacmaSummaryController extends Controller
{
    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage report') && !\Auth::user()->can('show report')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $period = $request->get('period', 'daily'); // daily, weekly, monthly, annual
        $date = $request->get('date', Carbon::today()->toDateString());
        $branchId = $request->get('branch_id', '');

        $refDate = Carbon::parse($date);

        switch ($period) {
            case 'weekly':
                $startDate = $refDate->copy()->startOfWeek()->toDateString();
                $endDate = $refDate->copy()->endOfWeek()->toDateString();
                $periodLabel = 'Weekly Summary (' . $startDate . ' to ' . $endDate . ')';
                break;
            case 'monthly':
                $startDate = $refDate->copy()->startOfMonth()->toDateString();
                $endDate = $refDate->copy()->endOfMonth()->toDateString();
                $periodLabel = 'Monthly Summary (' . $refDate->format('F Y') . ')';
                break;
            case 'annual':
                $startDate = $refDate->copy()->startOfYear()->toDateString();
                $endDate = $refDate->copy()->endOfYear()->toDateString();
                $periodLabel = 'Annual Summary (' . $refDate->format('Y') . ')';
                break;
            case 'daily':
            default:
                $period = 'daily';
                $startDate = $refDate->toDateString();
                $endDate = $refDate->toDateString();
                $periodLabel = 'Daily Summary (' . $startDate . ')';
                break;
        }

        $query = Sale::whereBetween('date_of_sale', [$startDate, $endDate])
            ->where('status', '!=', 0);

        if (!empty($branchId)) {
            $query->where('branch_id', $branchId);
        }

        $sales = $query->orderBy('date_of_sale', 'desc')->get();

        $documentFee = (float) $sales->sum('document_charges');
        $downPayment = (float) $sales->where('sale_type', 1)->sum('down_payment');
        $cashSales = (float) $sales->where('sale_type', 0)->sum('net_amount');
        $creditSales = (float) $sales->where('sale_type', 1)->sum(function ($s) {
            return (float) ($s->total_due_amount ?? $s->outstanding_amount ?? $s->net_amount ?? 0);
        });
        $grandTotal = $documentFee + $downPayment + $cashSales + $creditSales;

        // Grouped breakdown by date (or month for annual)
        $breakdown = $sales->groupBy(function ($item) use ($period) {
            $d = Carbon::parse($item->date_of_sale);
            return $period === 'annual' ? $d->format('Y-m') : $d->format('Y-m-d');
        })->map(function ($rows, $key) use ($period) {
            return [
                'label' => $period === 'annual' ? Carbon::parse($key . '-01')->format('F Y') : $key,
                'document_fee' => (float) $rows->sum('document_charges'),
                'down_payment' => (float) $rows->where('sale_type', 1)->sum('down_payment'),
                'cash_sales' => (float) $rows->where('sale_type', 0)->sum('net_amount'),
                'credit_sales' => (float) $rows->where('sale_type', 1)->sum(function ($s) {
                    return (float) ($s->total_due_amount ?? $s->outstanding_amount ?? $s->net_amount ?? 0);
                }),
                'count' => $rows->count(),
            ];
        })->values();

        $branches = Branch::orderBy('name')->pluck('name', 'id');

        return view('report.pacma_summary', compact(
            'period',
            'date',
            'branchId',
            'branches',
            'startDate',
            'endDate',
            'periodLabel',
            'documentFee',
            'downPayment',
            'cashSales',
            'creditSales',
            'grandTotal',
            'breakdown'
        ));
    }
}
