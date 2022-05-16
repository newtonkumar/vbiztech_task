<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!DOCTYPE html>
<html>
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Stripe Payment</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>    
    <body>
        <div class="container mt-4">
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header text-center font-weight-bold">
                    <h2>Product {{$productData->name}} Details</h2>
                </div>
                <div class="card-body">
                    <p>Product Name : {{$productData->name}}</p>
                    <p>Price : {{$productData->price}}</p>
                    <p>Description : {{$productData->description}}</p>
                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 bg-white border-b border-gray-200">
                                    <form name="order" id="payment-form" method="post" action="" data-secret="">
                                        {{ csrf_field() }}
                                        <div class="mb-4">
                                            <input type="hidden" value="{{$productData->price}}" id="" name="price" readonly>
                                        </div>
                                        <div>
                                            <label for="card-holder-name">Card Holder Name</label>    <br>
                                            <input type="text" name="card-holder-name" id="card-holder-name">
                                        </div>   
                                        <label for="card-element">Credit Card Or Debit card</label>
                                        <div id="card-element"></div>
                                        <div id="card-errors" role="alert"></div>
                                        <x-button>Buy Now</x-button>     
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </body>
</html>
</div>
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('sk_test_51KzcXrSCCfVd7n0xJfYFDUg2Y1ACNutPtTE05lBNrbxXcs4asGynOpRIhaSy8XtHiCFlpMdvMViwpH6c6K87k69K00J62YSj5R');
    var elements = stripe.elements();
    var style = {
        base:{
            color:'#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing:'antialiased',
            fontSize:'16px',
            '::placeholder':{
                color:'#aab7c4'
            }
        },
        invalid: {
            color:'#fa755a',
            iconColor:'#fa755a'
        }
    };
    var card = elements.create('card', {
        style: style
    });   

    card.mount('#card-element');
    card.on('change', function(event){
        var displayError = document.getElementById('card-errors');
        if(event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    var form = document.getElementById('payment-form');
    var clientSecret = form.dataset.secret;
    const cardHolderName = document.getElementById('card-holder-name');

    form.addEventListener('submit', async function(event){
        event.preventDefault();
        const {
            setInput,
            error
        } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: cardHolderName.value
                    }
                }
            }
        );
        if (error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            stripeTokenHandler(setupIntent.payment_method);
        }
    });
    
    function stripeTokenHandler(payment_method) {
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'paymentMethodId');
        hiddenInput.setAttribute('value', payment_method);
        form.appendChild(hiddenInput);

        form.submit();
    }
</script>
@endpush
</x-app-layout>