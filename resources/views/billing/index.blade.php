@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Subscription Plans</div>
                    <div class="card-body">
                        @if(session('message'))
                            <div class="alert alert-info">{{session('message')}}</div>
                        @endif
                        @if(!is_null($currentPlan))
                            @if($currentPlan->trial_ends_at)
                                <div class="alert alert-warning">Your trial period will end on {{$currentPlan->trial_ends_at->toDateString()}}</div>
                            @endif
                        @endif

                        <div class="row">
                        @foreach($plans as $plan)
                                <div class="col-md-4 text-center">
                                    <h3>{{$plan->name}}</h3>
                                    <h5>USD {{number_format($plan->price/100,2)}}/month</h5>
                                    <hr/>
                                    @if(is_null($currentPlan))
                                        <a href="{{route('checkout',$plan->id)}}" class="btn btn-primary" role="button">Subscribe to {{$plan->name}} plan</a>
                                    @else
                                        @if($currentPlan->stripe_plan == $plan->stripe_plan_id)
                                            @if($currentPlan->onGracePeriod())
                                                Your Plan will end on {{$currentPlan->ends_at->toDateString()}}
                                                <a href="{{route('resume')}}" class="btn btn-primary">Resume Plan</a>
                                            @else
                                                <a href="{{route('cancel')}}" class="btn btn-danger" onclick="return alert('Are You Confirm')">Cancel Plan</a>
                                            @endif
                                         @else
                                            <a href="{{route('checkout',$plan->id)}}" class="btn btn-primary" role="button">Subscribe to {{$plan->name}} plan</a>
                                        @endif
                                     @endif

                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
                <br/>
                <br/>

                <div class="card">
                    <div class="card-header">Payment Methods</div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Card Brand</th>
                                    <th>Expires at</th>
                                    <th>Country</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($paymentMethods as $pm)
                              <tr>
                                  <td>{{$pm->card->brand}}</td>
                                  <td>{{$pm->card->exp_month}} / {{$pm->card->exp_year}}</td>
                                  <td>{{$pm->card->country}}</td>
                                  @if($pm->id == auth()->user()->defaultPaymentMethod()->id)
                                   <td>Default Method</td>
                                      @else
                                      <td>
                                          <a href="{{route('payment-methods.default',$pm->id)}}">Mark As Default</a>
                                      </td>
                                  @endif
                              </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <a href="{{route('payment-methods.create')}}" class="btn btn-primary">Add Payment Method</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
