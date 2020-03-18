$(document).ready(function() {
	$(document).ready(function(){
		$('.sub_menu_header').click(function() {
			$('.sub_menu_header').toggleClass('open');
		});
	});

	$('.pay_close').click(function(){
		$('body').removeClass('new_fix');
	});

	$('.field__input').on('input', function() {
		var $field = $(this).closest('.field');
		if (this.value) {
			$field.addClass('field--not-empty');
		}
		else {
			$field.removeClass('field--not-empty');
		}
	});
});

$http = angular.injector(["ng"]).get("$http");

$('#js-currency-select').on('change', function(){
	currency_code = $(this).val();
	$http.post(APP_URL+'/set_session', {currency: currency_code}).then(function(response){
		location.reload();
	});
});

$('#js-language-select').on('change', function(){
	language_code = $(this).val();
	$http.post(APP_URL+'/set_session', {language: language_code
	}).then(function(response){
		location.reload();
	});
});

//Payout Preferences
app.controller('payout_preferences', ['$scope', '$http', function($scope, $http) {
	
    var stripe = Stripe(STRIPE_PUBLISH_KEY);

	// Check input is valid or not
    $scope.checkInValidInput = function(value) {
        return (value == undefined || value == 0 || value == '');
    };

	$scope.countryChanged = function() {
		if($scope.country == '') {
			$scope.payout_country = '';
	      	$scope.payout_currency = '';
		}
		else {
	      	$scope.payout_country = $scope.country;
	      	$scope.changeCurrency();
		}
	};

	// change currency based on country selected
    $scope.changeCurrency = function(reset = true) {
    	if(reset) {
			$scope.payout_currency = '';
    	}
		if($scope.payout_country == 'GB' && $scope.payout_currency == 'EUR') {
			$('.routing_number_cls').addClass('hide');
			$('.account_number_cls').html('IBAN');
		}
		else {
			$('.routing_number_cls').removeClass('hide');
			$('.account_number_cls').html('Account Number');
		}
	};

	// response handler function from for create stripe token
    $scope.stripeResponseHandler = function(result) {
		$("#stripe_errors").html("");
		if (result.error) {
			$("#stripe_errors").html(result.error.message); 
			/*if(result.error.code == "parameter_invalid_empty") { 
				$("#stripe_errors").html('Please fill all required fields');
			}*/
			return false;
		}

		document.getElementById("stripe_token").value = result.token.id;
		document.getElementById('payout_stripe').submit();
		setTimeout( () => $('button[type="button"]').attr('disabled',''),0);
    };

	$scope.resetErrors = function() {
		$scope.show_address_error = false;
		$scope.show_method_error = false;
		$scope.show_paypal_error = false;
		$scope.show_bank_error = false;
		$scope.address_error = false;
		$scope.city_error = false;
		$scope.postal_error = false;
		$scope.country_error = false;
		$scope.paypal_email_error = false;
		$scope.required_error = false;
	};

	$scope.nextStep = function(current_step) {
		$scope.resetErrors();
		if(current_step == 'address') {
			if ($('#payout_info_payout_country').val().trim() == '') {
		      	$scope.show_address_error = true;
		      	$scope.country_error = true;
		      	return false;
		    }
		    if ($('#payout_info_payout_address1').val().trim() == '') {
		      	$scope.show_address_error = true;
		      	$scope.address_error = true;
		      	return false;
		    }
		    if ($('#payout_info_payout_city').val().trim() == '') {
			    $scope.show_address_error = true;
		      	$scope.city_error = true;
		      	return false;
		    }
		    if ($('#payout_info_payout_zip').val().trim() == '') {
			    $scope.show_address_error = true;
		      	$scope.postal_error = true;
		      	return false;
		    }
		    
			$('#payout_popup-address').modal('hide');
			setTimeout( () => $('#payout_popup-methods').modal('show') ,250);
		}

		if(current_step == 'payout_method') {
		    if ($scope.checkInValidInput($scope.payout_method)) {
		    	$scope.show_method_error = true;
		    	$scope.method_error = true;		    	
				return false;
		    }
			$('#payout_popup-methods').modal('hide');
			if($scope.payout_method == 'stripe' && !$scope.country_list.hasOwnProperty($scope.country)) {
				$scope.country = '';
				$scope.countryChanged();
			}
			setTimeout( () => $('#payout_popup-'+$scope.payout_method).modal('show'),250);
		}

		if(current_step == 'update_paypal') {
		    if ($scope.checkInValidInput($scope.paypal_email)) {
		    	$scope.show_paypal_error = true;
		    	$scope.paypal_email_error = true;
				return false;
		    }
		    document.getElementById('payout_paypal').submit();
		}

		if(current_step == 'update_banktransfer') {
			var holder_name = $('#bank_holder_name').val().trim();
			var account_number = $('#bank_account_number').val().trim();
			var bank_name = $('#bankname').val().trim();
			var bank_location = $('#bank_location').val().trim();
			var bank_code = $('#bank_code').val().trim();
			if ($scope.checkInValidInput(holder_name) || $scope.checkInValidInput(account_number) || $scope.checkInValidInput(bank_name) || $scope.checkInValidInput(bank_location) || $scope.checkInValidInput(bank_code)) {
		      	$scope.show_bank_error = true;
		      	$scope.required_error = true;
		      	return false;
		    }

		    document.getElementById('payout_bank_transfer').submit();
		    setTimeout( () => $('button[type="button"]').attr('disabled',''),0);
		}

		if(current_step == 'update_stripe') {
		    stripe_token = document.getElementById("stripe_token").value;

	        if(stripe_token != '') {
	        	document.getElementById('payout_stripe').submit();
	        	setTimeout( () => $('button[type="button"]').attr('disabled',''),0);
	        	return true;
	        }

	        $scope.show_stripe_error = false;

			if($('#payout_info_payout_country1').val() == '') {
				$scope.show_stripe_error = true;
				$("#stripe_errors").html('Please fill all required fields');
				return false;
			}
			if($('#payout_info_payout_currency').val() == '') {
				$scope.show_stripe_error = true;
				$("#stripe_errors").html('Please fill all required fields');
				return false;
			}
			if($('#holder_name').val() == '') {
				$scope.show_stripe_error = true;
				$("#stripe_errors").html('Please fill all required fields');
				return false;
			}

			if($('#payout_info_payout_country1').val() == 'US' && $('#ssn_last_4').val().trim() == '') {
				$scope.show_stripe_error = true;
				$("#stripe_errors").html('Please fill all required fields');
				return false;
			}

			is_iban = $('#is_iban').val();
			is_branch_code = $('#is_branch_code').val();

			// bind bank account params to get stripe token
			var bankAccountParams = {
				country: $('#payout_info_payout_country1').val(),
				currency: $('#payout_info_payout_currency').val(),              
				account_number: $('#account_number').val(),
				account_holder_name: $('#holder_name').val(),
				account_holder_type: $('#holder_type').val()
			};

			// check whether iban supported country or not for bind routing number
			if(is_iban == 'No') {
				if(is_branch_code == 'Yes') {
					// here routing number is combination of routing number and branch code
					if($('#payout_info_payout_country1').val() != 'GB' && $('#payout_info_payout_currency').val() != 'EUR') {
						if($('#routing_number').val() == '') {
							$("#stripe_errors").html('Please fill all required fields');
							return false;
						}
						if($('#branch_code').val() == '') {
							$("#stripe_errors").html('Please fill all required fields');                
							return false;
						}

						bankAccountParams.routing_number = $('#routing_number').val()+'-'+$('#branch_code').val();
					}
				}
				else {
					if($('#payout_info_payout_country1').val() != 'GB' && $('#payout_info_payout_currency').val() != 'EUR') {
						if($('#routing_number').val() == '') {
							$("#stripe_errors").html('Please fill all required fields');                
							return false;
						}
						bankAccountParams.routing_number = $('#routing_number').val();
					}
				}
			}

			if($scope.show_stripe_error) {
				$("#stripe_errors").html('Please fill all required fields');
				return false;
			}

			stripe.createToken('bank_account', bankAccountParams).then($scope.stripeResponseHandler);
			return true;
		}
	};

	$(document).on('change',".payout_method",function() {
		$scope.payout_method = this.value;
	});

	$('[id$="_flash-container"]').on('click', '.alert-close', function() {
    	$(this).parent().parent().addClass('ng-hide');
    });
}]);

app.controller('help', ['$scope', '$http', function($scope, $http) {
	$('.help-nav .navtree-list .navtree-next').click(function() {
		var id = $(this).data('id');
		var name = $(this).data('name');
		$('.help-nav #navtree').addClass('active');    
		$('.help-nav #navtree').removeClass('not-active');
		$('.help-nav .subnav-list li:first-child a').attr('aria-selected', 'false');
		$('.help-nav .subnav-list').append('<li> <a class="subnav-item" href="#" data-node-id="0" aria-selected="true"> ' + name + ' </a> </li>');
		$('.help-nav #navtree-'+id).css({
			'display': 'block'
		});
	});

	$('.help-nav .navtree-list .navtree-back').click(function() {
		var id = $(this).data('id');
		var name = $(this).data('name');
		$('.help-nav #navtree').removeClass('active');
		$('.help-nav #navtree').addClass('not-active');
		$('.help-nav .subnav-list li:first-child a').attr('aria-selected', 'true');
		$('.help-nav .subnav-list li').last().remove();
		$('.help-nav #navtree-' + id).css({
			'display': 'none'
		});
	});

	$('#help_search').autocomplete({
		source: function(request, response) {
			$.ajax({
				url: APP_URL + "/ajax_help_search",
				type: "GET",
				dataType: "json",
				data: {
					term: request.term
				},
				success: function(data) {
					response(data);
					$(this).removeClass('ui-autocomplete-loading');
				}
			});
		},
		search: function() {
			$(this).addClass('loading');
		},
		open: function() {
			$(this).removeClass('loading');
		}
	})
	.autocomplete("instance")._renderItem = function(ul, item) {
		if (item.id != 0) {
			$('#help_search').removeClass('ui-autocomplete-loading');
			return $("<li>")
			.append("<a href='" + APP_URL + "/help/article/" + item.id + "/" + item.question + "' class='article-link article-link-panel link-reset'><div class='hover-item__content'><div class='col-middle-alt article-link-left'><i class='icon icon-light-gray icon-size-2 article-link-icon icon-description'></i></div><div class='col-middle-alt article-link-right'>" + item.value + "</div></div></a>")
			.appendTo(ul);
		}
		else {
			$('#help_search').removeClass('ui-autocomplete-loading');
			return $("<li style='pointer-events: none;'>")
			.append("<span class='article-link article-link-panel link-reset'><div class='hover-item__content'><div class='col-middle-alt article-link-left'><i class='icon icon-light-gray icon-size-2 article-link-icon icon-description'></i></div><div class='col-middle-alt article-link-right'>" + item.value + "</div></div></span>")
			.appendTo(ul);
		}
	};

}]);