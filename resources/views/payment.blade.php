<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Payment') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="container mt-4">
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header text-center font-weight-bold">
                    <h2>Product {{$productData->name}} Details</h2>
                    <p>Product Name : {{$productData->name}}</p>
                    <p>Price : {{$productData->price}}</p>
                    <p>Description : {{$productData->description}}</p>
                </div>
                <div class="card-body">
                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 bg-white border-b border-gray-200">
                                    <form name="order" id="payment-form" method="post" action="{{ route('charge')}}" data-secret="{{$intent->client_secret}}">
                                        {{ csrf_field() }}
                                        <div class="mb-4">
                                            <x-input type="hidden" value="{{$productData->price}}" id="" name="amount"/>
                                        </div>
                                        <div>
                                            <x-label for="card-holder-name" value="Card Holder Name"/>
                                            <x-input type="text" name="card-holder-name" id="card-holder-name"/>
                                        </div><br>
                                        <x-label for="card-element" value="Credit Card Or Debit card"/><br>
                                        <div id="card-element"></div><br>
                                        <div id="card-errors" role="alert"></div>
                                        <x-button data-secret="{{$intent->client_secret}}" type="submit" id="submit-button">Buy Now</x-button>     
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('extra-js')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Create a Stripe client.
        var stripe = Stripe('{{ env("STRIPE_KEY") }}');
        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                color: '#aab7c4'
                }
            },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {hidePostalCode: true, style: style});
        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

         // Handle real-time validation errors from the card Element.
         card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('submit-button')
        const clientSecret = cardButton.dataset.secret;
    
        cardButton.addEventListener('click', async (e) => {
            console.log('attempting');
            event.preventDefault();
            const {setupIntent, error} = await stripe.confirmCardSetup(
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
                paymentMethodHandler(setupIntent.payment_method);
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        // Submit the form with the token ID.
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            //form.submit();
        }

        function paymentMethodHandler(payment_method) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', payment_method);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    </script> 
    @endsection
</x-app-layout>