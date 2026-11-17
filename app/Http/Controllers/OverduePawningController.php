<?php

namespace App\Http\Controllers;

use App\Pawning;
use App\PawningInstallment;
use App\Customer;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OverduePawningController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage pawning'))
        {
            $today = Carbon::today();
            $overduePawningInstallments = PawningInstallment::whereDate('installment_date', '<', $today->format('Y-m-d'))
                                        ->where('status', 0)
                                        ->where('branch_id', \Auth::user()->branchId())
                                        ->get();

            if($overduePawningInstallments != null){
                foreach ($overduePawningInstallments as $overduePawningInstallment) {
                    $pawningInstallment = PawningInstallment::find($overduePawningInstallment->id);
                    $pawningInstallment->status = 2;
                    $pawningInstallment->update();
                }
            }

            $overduePawningInstallments = PawningInstallment::select('id', 'pawning_id',
                                                            DB::raw('MAX(installment_date) as overdue_date'),
                                                            DB::raw('COUNT(status) as overdue_installment_count'),
                                                            DB::raw('SUM(installment_amount) as overdue_amount'))
                                                            ->with('pawning.customer')
                                                            ->whereDate('installment_date', '<', $today->format('Y-m-d'))
                                                            ->where('status', 2)
                                                            ->where('sent_reminder', 0)
                                                            ->where('branch_id', '=', \Auth::user()->branchId())
                                                            ->orderBy('installment_date', 'DESC')
                                                            ->groupBy('pawning_id')
                                                            ->get();

            return view('overduePawning.index', compact('overduePawningInstallments'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit pawning'))
        {
            $jewellery = Jewellery::find($id);
            if($jewellery->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                     ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $jewellery->name = $request->name;
                $jewellery->save();
                return redirect()->route('jewellery.index')->with('success', __('Jewellery successfully updated !!!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Jewellery $jewellery)
    {
        if(\Auth::user()->can('delete pawning')){
            $jewellery->delete();

            return redirect()->route('timeperiods.index')->with(
                'success', 'Jewellery successfully deleted.'
            );
        }
        else
        {
            return redirect()->back()->with('error','Permission denied.');
        }


    }

    public function sendReminder($id){
        $reminder = PawningInstallment::find($id);
        $reminder->sent_reminder = 1;
        $reminder->update();
        return redirect()->back()->with('success', __('Reminder sent successfully !!!'));
    }


}
