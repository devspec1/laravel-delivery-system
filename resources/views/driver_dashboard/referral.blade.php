<title>Referrals</title>
@extends('template_driver_dashboard')
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;" ng-controller="user" ng-init="apply_referral='{{ $apply_referral }}'">
	<div class="page-lead separated--bottom  text--center text--uppercase" ng-cloak>
		<h1 class="flush-h1 flush" ng-if="apply_referral == 1"> @lang('messages.referrals.referral_header',['amount' => $driver_referral_amount]) </h1>
		<h1 class="flush-h1 flush" ng-if="apply_referral != 1"> @lang('messages.referrals.referral_not_available') </h1>
	</div>
	<div class="" style="padding:0px 15px;" ng-init="referral_code='{{ $result->referral_code }}'" ng-cloak>
		<div class="parter-info separated--bottom" style="padding: 0px 0px 15px;" ng-if="apply_referral == 1">
			<input type="hidden" name="user_id" value="{{ @Auth::user()->id}}" id="user_id">
			<h4 class="flush-h3"> {{trans('messages.referrals.referral_code')}} : <span class="font-weight-bold" style="font-weight: bold;"> @{{ referral_code }} </span> </h4>
		</div>
		<div class="parter-info separated--bottom" style="padding: 0px 0px 15px;">
			<h2 class="flush-h2" style="text-align: center;"> {{trans('messages.referrals.previous_referrals')}} </h2>
		</div>
		{{-- <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 25px 0px 15px;"  ng-init="all_referral_details={{ $all_referral_details }};currentPage=all_referral_details.current_page;totalPages=all_referral_details.last_page;"> --}}
        <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 25px 0px 15px;"  ng-init="getReferrals()">
			<div id="no-more-tables">
				<table class="col-sm-12 table-bordered table-striped table-condensed cf">
					<thead class="cf">
						<tr>
							<th class="width-60"> Driver Name </th>
							<th class="hide-sm"> Total Trips </th>
							<th class="hide-sm"> Trips Remaining </th>
							<th class="hide-sm"> Total Days </th>
							<th class="hide-sm"> Days Remaining </th>
							<th class="width-20"> Earnable Amount </th>
							<th class="width-20"> Status </th>
						</tr>
					</thead>
					{{-- <tbody class="all-trips-table" ng-repeat="referral_data in all_referral_details.data"> --}}
                    <tbody class="all-trips-table" ng-repeat="referral_data in all_referral_details">
						<tr class="trip-expand__origin collapsed" ng-cloak>
							<td data-title="Pickup">@{{ referral_data.referred_user_name }}</td>
							<td class="hide-sm" data-title="Driver">@{{ referral_data.trips }}</td>
							<td data-title="Fare"><span ></span>&nbsp;@{{ referral_data.remaining_trips }} </td>
							<td data-title="Car">@{{ referral_data.days }}</td>
							<td data-title="Car">@{{ referral_data.remaining_days }}</td>
							<td data-title="City" class="hide-sm">@{{ referral_data.earnable_amount }}</td>
							<td class="hide-sm" data-title="Payment Method">
								<span class="soft-half--sides">@{{ referral_data.payment_status }}</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding:25px;">
				<div class="pagination-buttons-container row-space-8 float--right" ng-cloak>
					<div class="results_count pagination inline-group btn-group btn-group--bordered" style="float: right;margin-top: 20px;">
						<div class="inline-group__item" ng-show="totalPages > 1">
							<referral-pagination></referral-pagination>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</div>
</main>
@stop