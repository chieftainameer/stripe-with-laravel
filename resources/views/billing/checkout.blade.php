@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Subscribe to {{$plan->name}} Plan</div>

                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-info">{{session('error')}}</div>
                        @endif
                        <form id="checkout-form" action="{{route('checkout.process')}}" method="POST">
                            @csrf
                            <input type="hidden" name="billing-plan-id" id="billing-plan-id" value="{{$plan->id}}" />
                            <input type="hidden" name="payment-method" id="payment-method" value="" />
                        <input id="card-holder-name" type="text">

                        <!-- Stripe Elements Placeholder -->
                        <div id="card-element"></div>
                            <br/>
                        <button class="bn btn-primary" id="card-button">
                            Pay ${{number_format($plan->price/100,2)}}
                        </button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $(document).ready(function(){
            let stripe = Stripe("{{env('STRIPE_KEY')}}");
            let elements = stripe.elements();
            let style={
                base: {
                    color: '#32325d',
                    fontSize: '16px',
                    '::placeholder':{
                        color:'#aab7c4'
                    }
                },
                invalid:{
                    color:'#fa755a',
                    iconColor:'#fa755a'
                }
            }
            let card = elements.create('card',{style:style});
            card.mount('#card-element')
            let paymentMethod = null
            $('#checkout-form').on('submit',function(e){
                if(paymentMethod){
                    return true;
                }
                stripe.confirmCardSetup("{{$intent->client_secret}}",
                    {
                        payment_method:{
                            card:card,
                            billing_details:{name: $('#card-holder-name').val()}
                        }
                    }
                ).then(function(result){
                    if(result.error){
                        console.log(result);
                        alert('error encountered');
                    }
                    else
                    {
                        paymentMethod = result.setupIntent.payment_method;
                        $('#payment-method').val(paymentMethod);
                        $('#checkout-form').submit();
                    }
                })
                return false
            });
        });
    </script>
    @endsection
