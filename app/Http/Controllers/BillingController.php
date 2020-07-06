<?php

namespace App\Http\Controllers;

use App\Plan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $currentPlan = auth()->user()->subscription('default') ?? NULL ;
        $paymentMethods = auth()->user()->paymentMethods();
        //dd(auth()->user()->defaultPaymentMethod()->id);
        //dd($paymentMethods);
        return view('billing.index',compact('plans','currentPlan','paymentMethods'));
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
            auth()->user()->newSubscription('default',$plan->stripe_plan_id)->create($r->input('payment-method'));
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
