<?php

namespace App\Http\Controllers;


use App\Payment;
use App\Plan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        //dd(auth()->user()->subscription('default')->trial_ends_at->toDateString());
        $plans = Plan::all();
        $payments = Payment::where('user_id',auth()->user()->id)->latest()->get();
        $currentPlan = auth()->user()->subscription('default') ?? NULL ;
        $paymentMethods = auth()->user()->paymentMethods();
        //dd(auth()->user()->defaultPaymentMethod()->id);
        //dd($paymentMethods);
        return view('billing.index',compact('plans','currentPlan','paymentMethods','payments'));
    }

    public function download_invoice($p)
    {
        $payment = Payment::findOrFail($p);
        $filename = storage_path('app/invoices/'.$payment->id.'.pdf');
        if (file_exists($filename)){
            return response()->download($filename);
        }
        else{
            abort(404);
        }

    }

    public function checkout($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);
        $intent = auth()->user()->createSetupIntent();
        $currentPlan = auth()->user()->subscription('default')->stripe_plan ?? NULL ;
        if(!is_null($currentPlan) && $currentPlan != $plan->stripe_plan_id){
            auth()->user()->subscription('default')->swap($plan->stripe_plan_id);
            return redirect()->route('billing');
        }
        return view('billing.checkout',compact('plan','intent'));
    }

    public function process_checkout(Request $r)
    {
        $plan = Plan::findOrFail($r->input('billing-plan-id'));
        try{
            //auth()->user()->newSubscription('default',$plan->stripe_plan_id)->trialDays(7)->create($r->input('payment-method'));
            auth()->user()->newSubscription('default',$plan->stripe_plan_id)->create($r->input('payment-method'));
            auth()->user()->update([
                'address-one' => $r->input('address_one'),
                'address-two' => $r->input('address_two'),
                'country' => $r->input('country'),
                'city' => $r->input('city'),
            ]);
            return redirect()->route('billing')->withMessage('Plan subscribed successfully');

        } catch (\Exception $e){
            return redirect()->back()->withError($e->getMessage());
        }
    }

    public function cancelPlan()
    {
        auth()->user()->subscription('default')->cancel();
        return redirect()->route('billing');
    }
    public function resumePlan()
    {
        auth()->user()->subscription('default')->resume();
        return redirect()->route('billing');
    }
}
