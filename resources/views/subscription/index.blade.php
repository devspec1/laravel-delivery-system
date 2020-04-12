@extends('template_driver_dashboard')

@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" ng-controller="payout_preferences">
	
	<main id="site-content" role="main">
		<div class="row-space-top-4 row-space-4">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-12">
						
					</div>
				</div>
			</div>
		</div>


        @if(isset($subscription) && $subscription != "canceled")

        <div class="row">
                <div style="display: flex; font-size: 140%; flex-direction: column; align-items: center; margin-top: 3em">
                <span> You are currently subscribed to <b> {{ $subscription->plan_name }} </b> plan </span>
                <a style="text-decoration: none; color: #175b9c; font-size: 110%" href="{{ action('SubscriptionController@cancelSubscription') }}"> Cancel </a>
   
            </div>
        </div>

        @else

        @if(isset($subMessage) && $subMessage != "")
        <span style="color: #197d11; margin-bottom: 2em; font-size: 110%"> {{ $subMessage }} </span>
        @endif
		<ul class="subscriptions">
                
                
                    <li class="founder">
                    
                        <div class="main_container">
                             <img src="{{ asset('images/car_image/car1.png') }}" class="topSubImage">        
                            <div class="mc_header">
                            <b>Driver Only</b>
                            <span>Ride share & Home delivery</span>
                            </div>
                               <div class="features_container">
                        
                            <ul>
                                <li class="feature_api_requests">18.5% booking fee (ride share)</li>
                                <li class="feature_updates">$1.50 flat commission for home deliveries</li>
                                <li class="feature_historical">$8.95 application fee <span class="hintIcon">
                                                <svg class="eapps-pricing-table-hint-icon-not-active" viewBox="0 0 14 14">
                                                    <g>
                                                        <path d="M7,0C3.1,0,0,3.1,0,7s3.1,7,7,7s7-3.1,7-7S10.9,0,7,0z M7,12.7c-3.2,0-5.7-2.6-5.7-5.7c0-3.2,2.6-5.7,5.7-5.7
                                                            s5.7,2.6,5.7,5.7C12.7,10.2,10.2,12.7,7,12.7z"></path>
                                                        <path d="M7,9.8c-0.5,0-0.8,0.4-0.8,0.8c0,0.5,0.4,0.8,0.8,0.8s0.8-0.4,0.8-0.8C7.8,10.1,7.5,9.8,7,9.8z"></path>
                                                        <path d="M7,2.5c-1.3,0-2.3,1-2.3,2.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6c0-0.6,0.5-1.1,1.1-1.1s1.1,0.5,1.1,1.1S7.6,5.9,7,5.9
                                                            c-0.4,0-0.6,0.3-0.6,0.6v1.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6V7.1c1-0.3,1.7-1.2,1.7-2.2C9.3,3.6,8.3,2.5,7,2.5z"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span></li>
                             
                               
                            </ul>
                        
                        </div>
                        
                            <div class="price">
                                                        
                                <div class="monthly_data">
                                    <span>$ <b>0</b> / month</span>
                
                                </div>
                                
                      
                            </div>
                            
                        	<a class="apply apply1 signup_link">Apply Now</a>
                    
                        </div>
                        
                     
                    
                    </li>
                  
                    
                    <li class="regular">
                        
                        <div class="main_container">
                            <img src="{{ asset('images/car_image/car2.png') }}" class="topSubImage">
                            <div class="ltWrp"> <span class="limitedTime">Limited Time</span></div>
                            <div class="mc_header">
                            	<b>Founder Member</b>
                                <span>Limited to 500 applicants</span>
                            </div>
                               <div class="features_container">
                        
                             <ul>
                                <li class="feature_api_requests"><b>Zero Commissions</b></li>
                                <li class="feature_updates">Drive team spill over<span class="hintIcon">
                                                <svg class="eapps-pricing-table-hint-icon-not-active" viewBox="0 0 14 14">
                                                    <g>
                                                        <path d="M7,0C3.1,0,0,3.1,0,7s3.1,7,7,7s7-3.1,7-7S10.9,0,7,0z M7,12.7c-3.2,0-5.7-2.6-5.7-5.7c0-3.2,2.6-5.7,5.7-5.7
                                                            s5.7,2.6,5.7,5.7C12.7,10.2,10.2,12.7,7,12.7z"></path>
                                                        <path d="M7,9.8c-0.5,0-0.8,0.4-0.8,0.8c0,0.5,0.4,0.8,0.8,0.8s0.8-0.4,0.8-0.8C7.8,10.1,7.5,9.8,7,9.8z"></path>
                                                        <path d="M7,2.5c-1.3,0-2.3,1-2.3,2.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6c0-0.6,0.5-1.1,1.1-1.1s1.1,0.5,1.1,1.1S7.6,5.9,7,5.9
                                                            c-0.4,0-0.6,0.3-0.6,0.6v1.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6V7.1c1-0.3,1.7-1.2,1.7-2.2C9.3,3.6,8.3,2.5,7,2.5z"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span></li>
                                <li class="feature_historical">2% Drive Team Income</li>
                                <li class="feature_support">3% Passenger Income  <span class="hintIcon">
                                                <svg class="eapps-pricing-table-hint-icon-not-active" viewBox="0 0 14 14">
                                                    <g>
                                                        <path d="M7,0C3.1,0,0,3.1,0,7s3.1,7,7,7s7-3.1,7-7S10.9,0,7,0z M7,12.7c-3.2,0-5.7-2.6-5.7-5.7c0-3.2,2.6-5.7,5.7-5.7
                                                            s5.7,2.6,5.7,5.7C12.7,10.2,10.2,12.7,7,12.7z"></path>
                                                        <path d="M7,9.8c-0.5,0-0.8,0.4-0.8,0.8c0,0.5,0.4,0.8,0.8,0.8s0.8-0.4,0.8-0.8C7.8,10.1,7.5,9.8,7,9.8z"></path>
                                                        <path d="M7,2.5c-1.3,0-2.3,1-2.3,2.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6c0-0.6,0.5-1.1,1.1-1.1s1.1,0.5,1.1,1.1S7.6,5.9,7,5.9
                                                            c-0.4,0-0.6,0.3-0.6,0.6v1.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6V7.1c1-0.3,1.7-1.2,1.7-2.2C9.3,3.6,8.3,2.5,7,2.5z"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span></li>
                                 <li class="feature_historical">10% Monthly Profit Pool  <span class="hintIcon">
                                                <svg class="eapps-pricing-table-hint-icon-not-active" viewBox="0 0 14 14">
                                                    <g>
                                                        <path d="M7,0C3.1,0,0,3.1,0,7s3.1,7,7,7s7-3.1,7-7S10.9,0,7,0z M7,12.7c-3.2,0-5.7-2.6-5.7-5.7c0-3.2,2.6-5.7,5.7-5.7
                                                            s5.7,2.6,5.7,5.7C12.7,10.2,10.2,12.7,7,12.7z"></path>
                                                        <path d="M7,9.8c-0.5,0-0.8,0.4-0.8,0.8c0,0.5,0.4,0.8,0.8,0.8s0.8-0.4,0.8-0.8C7.8,10.1,7.5,9.8,7,9.8z"></path>
                                                        <path d="M7,2.5c-1.3,0-2.3,1-2.3,2.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6c0-0.6,0.5-1.1,1.1-1.1s1.1,0.5,1.1,1.1S7.6,5.9,7,5.9
                                                            c-0.4,0-0.6,0.3-0.6,0.6v1.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6V7.1c1-0.3,1.7-1.2,1.7-2.2C9.3,3.6,8.3,2.5,7,2.5z"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span></li>
                                <li class="feature_support">Get Units in Ride on Drivers Owners Group</li>
                                <li class="feature_support"><b>Free Founders Starter Pack</b> <span class="eapps-pricing-table-hint">
                                            <span class="hintIcon">
                                                <svg class="eapps-pricing-table-hint-icon-not-active" viewBox="0 0 14 14">
                                                    <g>
                                                        <path d="M7,0C3.1,0,0,3.1,0,7s3.1,7,7,7s7-3.1,7-7S10.9,0,7,0z M7,12.7c-3.2,0-5.7-2.6-5.7-5.7c0-3.2,2.6-5.7,5.7-5.7
                                                            s5.7,2.6,5.7,5.7C12.7,10.2,10.2,12.7,7,12.7z"></path>
                                                        <path d="M7,9.8c-0.5,0-0.8,0.4-0.8,0.8c0,0.5,0.4,0.8,0.8,0.8s0.8-0.4,0.8-0.8C7.8,10.1,7.5,9.8,7,9.8z"></path>
                                                        <path d="M7,2.5c-1.3,0-2.3,1-2.3,2.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6c0-0.6,0.5-1.1,1.1-1.1s1.1,0.5,1.1,1.1S7.6,5.9,7,5.9
                                                            c-0.4,0-0.6,0.3-0.6,0.6v1.3c0,0.4,0.3,0.6,0.6,0.6s0.6-0.3,0.6-0.6V7.1c1-0.3,1.7-1.2,1.7-2.2C9.3,3.6,8.3,2.5,7,2.5z"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span></li>
                                 <li class="feature_historical">Founders Right</li>
                                <li class="feature_support">Limited to 500 approved applicants</li>
                               
                            </ul>
                        
                        </div>
                            
                            <div class="price">
                            
                                <div class="weekly_data">
                                    <span>$ <b>9.95</b> / week</span>
                                    <span style="color: #7f8588;
                                    font-size: 13px;
                                    font-weight: 400;
                                    line-height: 20px;">Founders save 35%</span>
                              
                                </div>
                                
                      
                                
                            </div>
                        
                        	<a class="apply apply2 signup_link" href="{{ action('SubscriptionController@getSubscriptionPlan', ['regular'])}}"  title="Sign up for the Business Plan">Apply Now</a>

                            <span style="    color: #7f8588;
                                font-size: 11px;
                                font-weight: 400;
                                line-height: 16px; margin-top: 12px">Cancel anytime</span>
                                                                        
                        </div>
                        
                     
                                        
                    </li>
                    
                                            
                </ul>

                @endif
	</main>

			
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
@endpush