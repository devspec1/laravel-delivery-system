<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => 'admin', 'middleware' =>'admin_auth'], function () {
	Route::get('login', 'AdminController@login')->name('admin_login');
});

Route::match(['get', 'post'],'admin/authenticate', 'AdminController@authenticate');

Route::group(['prefix' => (LOGIN_USER_TYPE=='company')?'company':'admin', 'middleware' =>'admin_guest'], function () {

	Route::redirect('/',LOGIN_USER_TYPE.'/dashboard');
	Route::get('dashboard', 'AdminController@index');

	if (LOGIN_USER_TYPE == 'admin') {
		Route::get('logout', 'AdminController@logout');
	}

	// Admin Users and permission routes
	Route::group(['middleware' => 'admin_can:manage_admin'], function() {
        Route::get('admin_user', 'AdminController@view');
        Route::match(array('GET', 'POST'),'add_admin_user', 'AdminController@add');
        Route::match(array('GET', 'POST'),'edit_admin_users/{id}', 'AdminController@update');
        Route::match(array('GET', 'POST'),'delete_admin_user/{id}', 'AdminController@delete');

        Route::get('roles', 'RolesController@index');
        Route::match(array('GET', 'POST'), 'add_role', 'RolesController@add');
        Route::match(array('GET', 'POST'), 'edit_role/{id}', 'RolesController@update')->where('id', '[0-9]+');
        Route::get('delete_role/{id}', 'RolesController@delete')->where('id', '[0-9]+');
    });

    // Manage Help Routes
    Route::group(['middleware' => 'admin_can:manage_help'],function () {
        Route::get('help_category', 'HelpCategoryController@index');
        Route::match(array('GET', 'POST'), 'add_help_category', 'HelpCategoryController@add');
        Route::match(array('GET', 'POST'), 'edit_help_category/{id}', 'HelpCategoryController@update')->where('id', '[0-9]+');
        Route::get('delete_help_category/{id}', 'HelpCategoryController@delete')->where('id', '[0-9]+');
        Route::get('help_subcategory', 'HelpSubCategoryController@index');
        Route::match(array('GET', 'POST'), 'add_help_subcategory', 'HelpSubCategoryController@add');
        Route::match(array('GET', 'POST'), 'edit_help_subcategory/{id}', 'HelpSubCategoryController@update')->where('id', '[0-9]+');
        Route::get('delete_help_subcategory/{id}', 'HelpSubCategoryController@delete')->where('id', '[0-9]+');
        Route::get('help', 'HelpController@index');
        Route::match(array('GET', 'POST'), 'add_help', 'HelpController@add');
        Route::match(array('GET', 'POST'), 'edit_help/{id}', 'HelpController@update')->where('id', '[0-9]+');
        Route::get('delete_help/{id}', 'HelpController@delete')->where('id', '[0-9]+');
        Route::post('ajax_help_subcategory/{id}', 'HelpController@ajax_help_subcategory')->where('id', '[0-9]+');
    });

	// Send message
	Route::group(['middleware' => 'admin_can:manage_send_message'], function() {
		Route::match(array('GET', 'POST'), 'send_message', 'SendmessageController@index')->name('admin.send_message');
        Route::post('get_send_users', 'SendmessageController@get_send_users');
        Route::get('get_send_merchants', 'SendmessageController@get_send_merchants');
	});
	
	// Manage Rider
	Route::get('rider', 'RiderController@index')->middleware('admin_can:view_rider');
	Route::match(array('GET', 'POST'), 'add_rider', 'RiderController@add')->middleware('admin_can:create_rider');
	Route::match(array('GET', 'POST'), 'edit_rider/{id}', 'RiderController@update')->middleware('admin_can:update_rider');
	Route::match(array('GET', 'POST'), 'delete_rider/{id}', 'RiderController@delete')->middleware('admin_can:delete_rider');

	// Manage Driver
	Route::get('driver', 'DriverController@index')->middleware('admin_can:view_driver');
	Route::match(array('GET', 'POST'), 'add_driver', 'DriverController@add')->middleware('admin_can:create_driver');
	Route::match(array('GET', 'POST'), 'edit_driver/{id}', 'DriverController@update')->middleware('admin_can:update_driver');
    Route::match(array('GET', 'POST'), 'delete_driver/{id}', 'DriverController@delete')->middleware('admin_can:delete_driver');
    
    //Manage Home Delivery
    Route::get('home_delivery', 'HomeDeliveryController@index');
    Route::match(array('GET', 'POST'),'add_home_delivery', 'HomeDeliveryController@add');
    Route::match(array('GET', 'POST'),'edit_home_delivery/{id}', 'HomeDeliveryController@update');
    Route::match(array('GET', 'POST'),'delete_home_delivery/{id}', 'HomeDeliveryController@delete');

    //Manage Merchants
    Route::get('merchants', 'MerchantsController@index');
    Route::get('merchant_orders/{id}', 'MerchantsController@merchant_order_details');
    Route::match(array('GET', 'POST'),'add_merchant', 'MerchantsController@add');
    Route::match(array('GET', 'POST'),'edit_merchant/{id}', 'MerchantsController@update');
	Route::match(array('GET', 'POST'),'delete_merchant/{id}', 'MerchantsController@delete');
	Route::post('search_phone_merchant', 'MerchantsController@search_phone_merchant');
	
	//Import Driver
	Route::match(array('GET', 'POST'), 'import_drivers', 'DriverController@import_drivers');
	
	// Manage Company
	Route::get('company', 'CompanyController@index')->middleware('admin_can:view_company');
	Route::match(array('GET', 'POST'), 'add_company', 'CompanyController@add')->middleware('admin_can:create_company');
	Route::match(array('GET', 'POST'), 'edit_company/{id}', 'CompanyController@update')->middleware('admin_can:update_company');
	Route::match(array('GET', 'POST'), 'delete_company/{id}', 'CompanyController@delete')->middleware('admin_can:delete_company');

	// Manage Statements
	Route::group(['middleware' =>  'admin_can:manage_statements'], function() {
		Route::post('get_statement_counts', 'StatementController@get_statement_counts');
		Route::get('statements/{type}', 'StatementController@index');
		Route::get('view_driver_statement/{driver_id}', 'StatementController@view_driver_statement');
		Route::get('driver_statement', 'StatementController@driver_statement');
		Route::get('statement_all', 'StatementController@custom_statement');
	});

	// Manage Location
	Route::group(['middleware' => 'admin_can:manage_locations'], function() {
		Route::get('locations', 'LocationsController@index');
	    Route::match(array('GET', 'POST'),'add_location', 'LocationsController@add')->name('admin.add_location');
	    Route::match(array('GET', 'POST'),'edit_location/{id}', 'LocationsController@update')->name('admin.edit_location');
	    Route::get('delete_location/{id}', 'LocationsController@delete');
	});

    // Manage Peak Based Fare Details
	Route::group(['middleware' => 'admin_can:manage_peak_based_fare'], function() {
		Route::get('manage_fare', 'ManageFareController@index');
	    Route::match(array('GET', 'POST'),'add_manage_fare', 'ManageFareController@add')->name('admin.add_manage_fare');
	    Route::match(array('GET', 'POST'),'edit_manage_fare/{id}', 'ManageFareController@update')->name('admin.edit_manage_fare');
	    Route::get('delete_manage_fare/{id}', 'ManageFareController@delete');
	});

	// Manage Toll fare Details
	Route::get('additional-reasons', 'TollReasonController@index')->middleware('admin_can:view_additional_reason');
    Route::match(array('GET', 'POST'),'add-additional-reason', 'TollReasonController@add')->middleware('admin_can:create_additional_reason');
    Route::match(array('GET', 'POST'),'edit-additional-reason/{id}', 'TollReasonController@update')->middleware('admin_can:update_additional_reason');
    Route::get('delete-additional-reason/{id}', 'TollReasonController@delete')->middleware('admin_can:delete_additional_reason');

	// Map
	Route::group(['middleware' =>  'admin_can:manage_map'], function() {
		Route::match(array('GET', 'POST'), 'map', 'MapController@index');
		Route::match(array('GET', 'POST'), 'mapdata', 'MapController@mapdata');
	});
	Route::group(['middleware' =>  'admin_can:manage_heat_map'], function() {
		Route::match(array('GET', 'POST'), 'heat-map', 'MapController@heat_map');
		Route::match(array('GET', 'POST'), 'heat-map-data', 'MapController@heat_map_data');
	});

	// Manage Vehicle Type
	Route::group(['middleware' =>  'admin_can:manage_vehicle_type'], function() {
		Route::get('vehicle_type', 'VehicleTypeController@index');
		Route::match(array('GET', 'POST'), 'add_vehicle_type', 'VehicleTypeController@add');
		Route::match(array('GET', 'POST'), 'edit_vehicle_type/{id}', 'VehicleTypeController@update');
		Route::match(array('GET', 'POST'), 'delete_vehicle_type/{id}', 'VehicleTypeController@delete');
	});

	// Manage Referrals Routes
	Route::group(['prefix' => 'referrals'], function() {
		Route::get('rider', 'ReferralsController@index')->middleware('admin_can:manage_rider_referrals');
		Route::get('driver', 'ReferralsController@index')->middleware('admin_can:manage_driver_referrals');
		Route::get('{id}', 'ReferralsController@referral_details');
	});

	// Manage Vehicle
	Route::group(['middleware' =>  'admin_can:manage_vehicle'], function() {
		Route::get('vehicle', 'VehicleController@index');
		Route::match(array('GET', 'POST'), 'add_vehicle', 'VehicleController@add');
		Route::post('manage_vehicle/{company_id}/get_driver', 'VehicleController@get_driver');
		Route::match(array('GET', 'POST'), 'edit_vehicle/{id}', 'VehicleController@update');
		Route::match(array('GET', 'POST'), 'delete_vehicle/{id}', 'VehicleController@delete');
	});

	// Trips
	Route::group(['middleware' =>  'admin_can:manage_trips'], function() {
		Route::match(array('GET', 'POST'), 'trips', 'TripsController@index');
		Route::get('view_trips/{id}', 'TripsController@view');
		Route::post('trips/payout/{id}', 'TripsController@payout');
		Route::get('trips/export/{from}/{to}', 'TripsController@export');
	});

	// Manage Company Payout Routes
	Route::group(['middleware' =>  'admin_can:manage_company_payment'], function() {
		Route::get('payout/company/overall', 'CompanyPayoutController@overall_payout');
		Route::get('weekly_payout/company/{company_id}', 'CompanyPayoutController@weekly_payout');
		Route::get('per_week_report/company/{company_id}/{start_date}/{end_date}', 'CompanyPayoutController@payout_per_week_report');
		Route::get('per_day_report/company/{company_id}/{date}', 'CompanyPayoutController@payout_per_day_report');
		Route::post('make_payout/company', 'CompanyPayoutController@payout_to_company');
	});

	// Manage Driver Payout Routes
	Route::group(['middleware' =>  'admin_can:manage_driver_payments'], function() {
		Route::get('payout/overall', 'PayoutController@overall_payout');
		Route::get('weekly_payout/{driver_id}', 'PayoutController@weekly_payout');
		Route::get('per_week_report/{driver_id}/{start_date}/{end_date}', 'PayoutController@payout_per_week_report');
		Route::get('per_day_report/{driver_id}/{date}', 'PayoutController@payout_per_day_report');
		Route::post('make_payout', 'PayoutController@payout_to_driver');
	});

	// Manage Subscribed Drivers
	Route::get('subscriptions/driver', 'SubscribedDriverController@index');
	Route::match(array('GET', 'POST'), 'subscriptions/edit_driver/{id}', 'SubscribedDriverController@update');

	// Manage Subscription Plans
	Route::get('subscriptions/plan', 'SubscriptionPlanController@index');
	Route::match(array('GET', 'POST'), 'subscriptions/add_plan', 'SubscriptionPlanController@add');
	Route::match(array('GET', 'POST'), 'subscriptions/edit_plan/{id}', 'SubscriptionPlanController@update');
	Route::match(array('GET', 'POST'), 'subscriptions/delete_plan/{id}', 'SubscriptionPlanController@delete');
	
	// Manage Wallet
	Route::group(['prefix' => 'wallet', 'middleware' =>  'admin_can:manage_wallet'], function() {
		Route::get('{user_type}', 'WalletController@index')->name('wallet');
		Route::match(array('GET', 'POST'), 'add/{user_type}', 'WalletController@add')->name('add_wallet');
		Route::match(array('GET', 'POST'), 'edit/{user_type}/{id}', 'WalletController@update')->where('id', '[0-9]+')->name('edit_wallet');
		Route::get('delete/{user_type}/{id}', 'WalletController@delete')->where('id', '[0-9]+')->name('delete_wallet');
	});
	
	// Owe Amount
	Route::group(['middleware' =>  'admin_can:manage_owe_amount'], function() {
		Route::match(array('GET', 'POST'), 'owe', 'OweController@index')->name('owe');
		Route::match(array('GET', 'POST'), 'company_owe/{id}', 'OweController@company_index')->name('owe');
		Route::get('details/{type}', 'OweController@owe_details')->name('owe_details');
		Route::get('update_driver_payment', 'OweController@update_payment')->name('update_payment');
		Route::post('update_owe_payment', 'OweController@updateOwePayment')->name('update_owe_payment');
		Route::post('update_company_payment', 'OweController@update_company_payment')->name('update_company_payment');
	});

	// Company Owe amount
	Route::get('driver_payment', 'OweController@driver_payment')->name('driver_payment');

	// Manage Promo Code
	Route::group(['middleware' =>  'admin_can:manage_promo_code'], function() {
		Route::get('promo_code', 'PromocodeController@index');
		Route::match(array('GET', 'POST'), 'add_promo_code', 'PromocodeController@add');		
		Route::match(array('GET', 'POST'), 'edit_promo_code/{id}', 'PromocodeController@update')->where('id', '[0-9]+');
		Route::get('delete_promo_code/{id}', 'PromocodeController@delete');
	});

	// Payments
	Route::group(['middleware' =>  'admin_can:manage_payments'], function() {
		Route::match(array('GET', 'POST'), 'payments', 'PaymentsController@index');
		Route::get('view_payments/{id}', 'PaymentsController@view');
		Route::get('payments/export/{from}/{to}', 'PaymentsController@export');
	});

	// Cancelled Trips
	Route::group(['middleware' =>  'admin_can:manage_cancel_trips'], function() {
		Route::get('cancel_trips', 'TripsController@cancel_trips');
	});

	// Manage Cancel reasons
	Route::get('cancel-reason', 'CancelReasonController@index')->middleware('admin_can:view_manage_reason');
	Route::match(array('GET', 'POST'), 'add-cancel-reason', 'CancelReasonController@add')->middleware('admin_can:create_manage_reason');
	Route::match(array('GET', 'POST'), 'edit-cancel-reason/{id}', 'CancelReasonController@update')->where('id', '[0-9]+')->middleware('admin_can:update_manage_reason');
	Route::get('delete-cancel-reason/{id}', 'CancelReasonController@delete')->middleware('admin_can:delete_manage_reason');

	// Manage Rating
	Route::group(['middleware' =>  'admin_can:manage_rating'], function() {
		Route::get('rating', 'RatingController@index');
		Route::get('delete_rating/{id}', 'RatingController@delete');
	});

	// Manage fees
	Route::group(['middleware' =>  'admin_can:manage_fees'], function() {
		Route::match(array('GET', 'POST'), 'fees', 'FeesController@index');
	});

	// Manage Referral Settings
	Route::group(['middleware' =>  'admin_can:manage_referral_settings'], function() {
		Route::get('referral_settings', 'ReferralSettingsController@index');
		Route::post('update_referral_settings', 'ReferralSettingsController@update');
    });
    
    // Manage Subscriptions Settings
	Route::group(['middleware' =>  'admin_can:manage_referral_settings'], function() {
		Route::get('subscriptions_settings', 'ReferralSettingsController@index');
		Route::post('drivers_subscriptions', 'ReferralSettingsController@update');
	});

	// SiteSetting
	Route::match(array('GET', 'POST'), 'site_setting', 'SiteSettingsController@index')->middleware('admin_can:manage_site_settings');
	
	// Manage Api credentials
	Route::match(array('GET', 'POST'), 'api_credentials', 'ApiCredentialsController@index')->middleware('admin_can:manage_api_credentials');

	// Manage Payment Gateway
	Route::group(['middleware' =>  'admin_can:manage_payment_gateway'], function() {
		Route::match(array('GET', 'POST'), 'payment_gateway', 'PaymentGatewayController@index');
	});

	// Request
	Route::group(['middleware' =>  'admin_can:manage_requests'], function() {
		Route::get('detail_request/{id}', 'RequestController@detail_request');
		Route::match(array('GET', 'POST'), 'request', 'RequestController@index');
	});

	// Join us management
	Route::group(['middleware' =>  'admin_can:manage_join_us'], function() {
		Route::match(array('GET', 'POST'), 'join_us', 'JoinUsController@index');
	});

	// Manage Static pages
	Route::group(['middleware' =>  'admin_can:manage_static_pages'], function() {
		Route::get('pages', 'PagesController@index');
		Route::match(array('GET', 'POST'), 'add_page', 'PagesController@add');
		Route::match(array('GET', 'POST'), 'edit_page/{id}', 'PagesController@update')->where('id', '[0-9]+');
		Route::get('delete_page/{id}', 'PagesController@delete')->where('id', '[0-9]+');
	});

	// Manage Meta
	Route::group(['middleware' =>  'admin_can:manage_metas'], function() {
		Route::match(array('GET', 'POST'), 'metas', 'MetasController@index');
		Route::match(array('GET', 'POST'), 'edit_meta/{id}', 'MetasController@update')->where('id', '[0-9]+');
	});

	// Manage Currency Routes
	Route::group(['middleware' =>  'admin_can:manage_currency'], function() {
		Route::get('currency', 'CurrencyController@index');
		Route::match(array('GET', 'POST'), 'add_currency', 'CurrencyController@add');
		Route::match(array('GET', 'POST'), 'edit_currency/{id}', 'CurrencyController@update')->where('id', '[0-9]+');
		Route::get('delete_currency/{id}', 'CurrencyController@delete')->where('id', '[0-9]+');
	});

	// Manage Language Routes
	Route::group(['middleware' =>  'admin_can:manage_language'], function() {
		Route::get('language', 'LanguageController@index');
		Route::match(array('GET', 'POST'), 'add_language', 'LanguageController@add');
		Route::match(array('GET', 'POST'), 'edit_language/{id}', 'LanguageController@update')->where('id', '[0-9]+');
		Route::get('delete_language/{id}', 'LanguageController@delete')->where('id', '[0-9]+');
	});

	// Manage Country
	Route::group(['middleware' => 'admin_can:manage_country'],function () {
        Route::get('country', 'CountryController@index');
        Route::match(array('GET', 'POST'), 'add_country', 'CountryController@add');
        Route::match(array('GET', 'POST'), 'edit_country/{id}', 'CountryController@update')->where('id', '[0-9]+');
        Route::get('delete_country/{id}', 'CountryController@delete')->where('id', '[0-9]+');
    });

	// Manual Booking
    Route::group(['middleware' => 'admin_can:manage_manual_booking'],function () {
        Route::get('manual_booking/{id?}', 'ManualBookingController@index');
        Route::post('manual_booking/store', 'ManualBookingController@store');
        Route::post('search_phone', 'ManualBookingController@search_phone');
        Route::post('search_cars', 'ManualBookingController@search_cars');
        Route::post('get_driver', 'ManualBookingController@get_driver');
        Route::post('driver_list', 'ManualBookingController@driver_list');
        Route::get('later_booking', 'LaterBookingController@index');
        Route::post('immediate_request', 'LaterBookingController@immediate_request');
        Route::post('manual_booking/cancel', 'LaterBookingController@cancel');
    });
	
	// Manage Email Settings Routes
	Route::match(array('GET', 'POST'), 'email_settings', 'EmailController@index')->middleware(['admin_can:manage_email_settings']);
    Route::match(array('GET', 'POST'), 'send_email', 'EmailController@send_email')->middleware(['admin_can:manage_send_email']);
});