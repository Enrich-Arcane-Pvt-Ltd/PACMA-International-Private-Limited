<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\Asset;
use App\GeneralLedgerAccount;
use App\Setting;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $currencySymbol;

    public function __construct()
    {
        $this->currencySymbol = Setting::where('name', 'site_currency_symbol')->value('value');
    }

    public function index()
    {
        if (\Auth::user()->can('manage assets')) {
            // $assets = Asset::where('created_by', '=', \Auth::user()->creatorId())->get();
            // $assets = Asset::where('branch_id', \Auth::user()->branchId())->get();

            // $accounts = ChartOfAccount::where('section_id', 3)->where('status', 1)->get();
            $accounts = GeneralLedgerAccount::where('account_type_id', 4)->where('status', 1)->get();

            $assets = [];
            foreach ($accounts as $account) {
                $transactions = AccountTransaction::select(\DB::raw('SUM(CASE WHEN debit_credit = 1 THEN amount ELSE -amount END) AS amount'))
                    ->where('status', 1)
                    ->where('branch_id', \Auth::user()->branchId())
                    ->where('chart_of_account_id', $account->id)
                    ->get();
                foreach ($transactions as $transaction) {
                    $amount = $transaction->amount;
                }
                $assets[] = array('id' => $account->id, 'name' => $account->account_name, 'amount' => $amount);
            }

            $currencySymbol = $this->currencySymbol;

            return view('asset.index', compact('assets', 'currencySymbol'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create assets')) {
            return view('asset.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create assets')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'amount' => 'required',
                    'purchase_date' => 'required',
                    'supported_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $asset                 = new Asset();
            $asset->asset_id       = $this->assetNumber();
            $asset->name           = $request->name;
            $asset->amount         = $request->amount;
            $asset->purchase_date  = $request->purchase_date;
            $asset->supported_date = $request->supported_date;
            $asset->description    = $request->description;
            $asset->branch_id      = \Auth::user()->branchId();
            $asset->status         = 1;
            $asset->created_by     = \Auth::user()->authId();
            $asset->save();

            return redirect()->route('asset.index')->with('success', __('Asset successfully created !!!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    function assetNumber()
    {
        // $latest = Asset::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        $latest = Asset::latest()->first();

        if (!$latest) {
            return 1;
        }
        return $latest->id + 1;
    }


    public function edit($id)
    {

        if (\Auth::user()->can('edit assets')) {
            $asset = Asset::find($id);
            return view('asset.edit', compact('asset'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit assets')) {
            $asset = Asset::find($id);
            // if($asset->created_by == \Auth::user()->creatorId())
            // {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'amount' => 'required',
                    'purchase_date' => 'required',
                    'supported_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $asset->name           = $request->name;
            $asset->purchase_date  = $request->purchase_date;
            $asset->supported_date = $request->supported_date;
            $asset->amount         = $request->amount;
            $asset->description    = $request->description;
            $asset->save();

            return redirect()->route('asset.index')->with('success', __('Asset successfully updated !!!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete assets')) {
            $asset = Asset::find($id);
            // if($asset->created_by == \Auth::user()->creatorId())
            // {
            $asset->delete();
            return redirect()->back()->with('success', __('Asset successfully deleted !!!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
