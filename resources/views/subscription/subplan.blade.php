@extends('template_driver_dashboard')

@section('main')

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" ng-controller="payout_preferences">
	<div class="page-lead separated--bottom  text--center text--uppercase">
		<h1 class="flush-h1 flush">Subscription </h1>
	</div>
    <div class="row justify-content-center" style="display: flex; margin-top: 2em; margin-left: 1em">
        <div style="display: flex; flex-direction: column;width: 45%; margin: auto; padding-left: 1em; padding-right: 1em; margin-top: 0; margin-bottom: 0">
            <span style="font-family: 'MontserratBold'; color: #1B187F; font-weight: 400; font-size: 150%;"> PLATINUM</span>
            <span style="font-family: 'MontserratReg'; font-weight: 600; font-size: 270%; white-space: nowrap"> Founder Member </span>
            <span style="font-family: 'MontserratReg'; color: #1B187F; font-weight: 600; font-size: 400%"> $385 </span>
            <span style="font-family:'MontserratReg'; font-weight: 400; font-size: 140%"> <span style="color: #0acc21; "> Save 26%  </span><span style="color: #bcc4be";> per year </span></span>
            <div style="margin-top: 1.4em; padding-bottom: 1.4em; border-bottom: 1px solid rgba(0, 0, 0, 0.1);  display: flex; width: 100%; justify-content: space-between;font-weight: bold; font-size: 140%;font-family: 'MontserratReg';">
              <span>GST (10%)</span> <span> $38.50</span>
            </div>
            <div style="margin-top: 1.4em; margin-bottom: 1.4em; display: flex; width: 100%; justify-content: space-between;font-weight: 600; font-size: 140%;font-family: 'MontserratReg';">
              <span></span> <span> $423.50</span>
            </div>
            <img style="margin-left: auto; margin-right: auto; height: 30em; width: 30em" src="{{ asset('images/car_image/car3.png') }}">

        </div>
        <div id="cardWrp"> <div class="col-md-12 card">
        	
                <form action="#" method="post" id="payment-form">
                    @csrf                    
                <span class="cardLabel2"> Pay by Card</span>

          <div class="fieldset">
            
             <input id="card-card-email" data-tid="elements_examples.form.email_label" class="field" type="email" placeholder="Email" required="" autocomplete="off"> 
          </div>
          <span class="cardLabel1">Card information</span>
          <div class="fieldset">
            <div id="card-card-number" class="field empty StripeElement"></div>
            <div id="card-card-expiry" class="field empty StripeElement"></div>
            <div id="card-card-cvc" class="field empty half-width StripeElement"></div>
          </div>
          <span class="cardLabel1">Name on card</span>
          <div class="fieldset">
            <input id="card-name" data-tid="elements_examples.form.name_label" class="field" type="text"  required="" autocomplete="off">
          </div>
           <span class="cardLabel1">Country / Region</span>
          <div class="fieldset">
            <select id="card-country" class="field"><option value="Australia">Australia</option></select>
        
          </div>


          <button type="submit" data-tid="elements_examples.form.pay_button">SUBSCRIBE</button>


        </form>
            </div>
        </div>
    </div>
</div>


@endsection
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>

<script type="text/javascript">
	let payout_errors = {!! count($errors->getMessages()) !!};
	let payout_method = '{!! old("payout_method") !!}';
	if(payout_errors > 0 && '{{Auth::user()->company_id <= 1}}' && payout_method != '') {
		$('#payout_popup-'+payout_method).modal('show');
	}
</script>
<script>

  $(function() {
 

  var stripe = Stripe(STRIPE_PUBLISH_KEY);


   var elements = stripe.elements({
    fonts: [
      {
        cssSrc: 'https://fonts.googleapis.com/css?family=Quicksand',
      },
    ],
    // Stripe's examples are localized to specific languages, but if
    // you wish to have Elements automatically detect your user's locale,
    // use `locale: 'auto'` instead.
    locale: window.__exampleLocale,
  });

    // Create a Stripe client.


// Create an instance of Elements.
var elementStyles = {
    base: {
      color: '#46464a',
      fontWeight: 500,
      top: '5px!important',
      fontFamily: 'Montserrat, Open Sans, Segoe UI, sans-serif',
      fontSize: '13px',
      fontSmoothing: 'antialiased',

      ':focus': {
        color: '#8a91a1'
  
      },

      '::placeholder': {
        color: '#8a91a1',
      },

      ':focus::placeholder': {
        color: '#a5abb8'

      },
    },
    invalid: {
      color: '#fa755a',
      border: '1px solid #870808',
      ':focus': {
        color: '#FA755A',
        background: '#870808'
      },
      '::placeholder': {
        color: '#FFCCA5',
      },
    },
  };

  var elementClasses = {
    focus: 'focus',
    empty: 'empty',
    invalid: 'invalid',
  };

  var cardNumber = elements.create('cardNumber', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardNumber.mount('#card-card-number');

  var cardExpiry = elements.create('cardExpiry', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardExpiry.mount('#card-card-expiry');

  var cardCvc = elements.create('cardCvc', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardCvc.mount('#card-card-cvc');

  registerElements([cardNumber, cardExpiry, cardCvc], 'card');

// Handle real-time validation errors from the card Element.


// Handle form submission.
var form = document.getElementById('payment-form');

form.addEventListener('submit', function(event) {
  // We don't want to let default form submission happen here,
  // which would refresh the page.
  event.preventDefault();

  var b = 1;
  $("#payment_form .required").each(function() {
    $(this).css("border", "1px solid rgba(0, 0, 0, 0.18)");
    if(!$(this).val()) {
      $(this).css("border", "1px solid red");
      b = 0;
    }
  })

  if(b) {
     var mail = $("#card-card-email").val();
      var exp = $("#card-card-expiry").val().split("/");
      console.log(exp);
      stripe.createPaymentMethod({
        type: 'card',
        card: cardNumber,
        billing_details: {
          email: mail,
        },
      }).then(stripePaymentMethodHandler);

  }
});

function stripePaymentMethodHandler(result, email) {
  if (result.error) {
    console.log("STRIPE ERROR - " . result.error);
  } else {
    // Otherwise send paymentMethod.id to your server

    

    var mail = $("#card-card-email").val();
    var name = $("#card-name").val();
    var country = $("#card-country").val();

    fetch('/create-customer', {
      method: 'post',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        email: mail,
        card_name: name,
        country: country,
        payment_method: result.paymentMethod.id,
        "_token": $("#payment-form input[type=hidden]").val()
      }),
    }).then(function(result) {
     return result.json();
    }).then(function(customer) {
    	console.log(customer);
      window.location.reload();
      // The customer has been created
    });
  }
}
function registerElements(elements, exampleName) {
  var formClass = '.' + exampleName;
  var example = document.querySelector(formClass);

  var form = example.querySelector('form');
  var resetButton = example.querySelector('a.reset');
  var error = form.querySelector('.error');
  //var errorMessage = error.querySelector('.message');

  function enableInputs() {
    Array.prototype.forEach.call(
      form.querySelectorAll(
        "input[type='text'], input[type='email'], input[type='tel']"
      ),
      function(input) {
        input.removeAttribute('disabled');
      }
    );
  }
}
  })
</script>
@endpush