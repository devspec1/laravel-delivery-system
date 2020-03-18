<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\Helper\FacebookHelper;
use App\Models\Admin;
use App\Models\CarType;
use App\Models\Language;
use App\Models\SiteSettings;
use App\Models\EmailSettings;
use App\Models\ApiCredentials;
use App\Models\PaymentGateway;
use App\Models\Fees;
use App\Models\ReferralSetting;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Config;
use DB;
use Session;
use App;
use View;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		define('EMAIL_LOGO_URL', 'images/logo.png');
		define('LOGIN_USER_TYPE', request()->segment(1));

		/*logger('requested URL : '.request()->fullUrl());
		if(request()->isMethod('POST')) {
			logger('Post Method Params : '.json_encode(request()->post()));
		}*/

		Schema::defaultStringLength(191);

		if (env('DB_DATABASE') != '') {
			$this->bindModels();
			$this->shareCommonData();

			/*\DB::listen(function($sql) {
	            logger($sql->sql);
	        });*/

			// Configuration for data table pdf export
            config(['datatables-buttons.pdf_generator' => 'snappy']);

            if(!in_array(request()->segment(1),['admin','company','install'])) {
            	Config::set(['cache.default' => 'file']);
            }

			if (Schema::hasTable('site_settings')) {

				$site_settings = DB::table('site_settings')->get();
				View::share('logo_url', url('images/logos/' . $site_settings[3]->value).'?v='.str_random(4));
				define('LOGO_URL', 'images/logos/' . $site_settings[3]->value);
				define('PAGE_LOGO_URL', 'images/logos/' . $site_settings[4]->value);
				View::share('favicon', url('images/logos/' . $site_settings[5]->value).'?v='.str_random(5));
				View::share('site_name', $site_settings[0]->value);
				define('SITE_NAME', $site_settings[0]->value);

				define('MANUAL_BOOK_CONTACT', '+'.$site_settings[9]->value.' '.$site_settings[8]->value);

				define('PAYPAL_CURRENCY_CODE', $site_settings[1]->value);
				define('PHP_DATE_FORMAT','Y-m-d');
				define('SITE_URL',$site_settings[10]->value);
				define('Driver_Km', $site_settings[6]->value);
			}
			if (Schema::hasTable('country')) {
				$country = DB::table('country')->get();
				View::share('country', $country);

			}
			if (Schema::hasTable('car_type')) {
				$car_type = CarType::where('status', 'Active')->get();
				View::share('car_type', $car_type);

			}

			if (Schema::hasTable('api_credentials')) {

				$api_credentials = resolve('api_credentials');

				// For Google Key
				$google_map_result = $api_credentials->where('site', 'GoogleMap');
				define('MAP_KEY', $google_map_result->where('name','key')->first()->value);
				define('MAP_SERVER_KEY', $google_map_result->where('name','server_key')->first()->value);
				View::share('map_key', $google_map_result->where('name','key')->first()->value);

				//For facebook
				$facebook_result = $api_credentials->where('site', 'Facebook');
				define('FB_CLIENT_ID', $facebook_result->where('name','client_id')->first()->value);

	        	// Share Google Credentials
	        	$google_result =  $api_credentials->where('site','Google');
	        	define('GOOGLE_CLIENT_ID', $google_result->where('name','client_id')->first()->value);

				// For Twillo Key
				$twillo_result = $api_credentials->where('site', 'Twillo');

				define('TWILLO_SID', $twillo_result->where('name','sid')->first()->value);
				define('TWILLO_TOKEN', $twillo_result->where('name','token')->first()->value);
				define('TWILLO_FROM', $twillo_result->where('name','from')->first()->value);

				// For FCM Key
				$fcm_result = $api_credentials->where('site', 'FCM');
				Config::set(['fcm.http' => [
					'server_key' => $fcm_result->where('name','server_key')->first()->value,
					'sender_id' => $fcm_result->where('name','sender_id')->first()->value,
					'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
					'server_group_url' => 'https://android.googleapis.com/gcm/notification',
					'timeout' => 10,
				],
				]);

				// For Facebook app id and secret
				$fb_result = $api_credentials->where('site', 'Facebook');
				Config::set([
					'facebook' => [
						'client_id' => $fb_result->where('name','client_id')->first()->value,
						'client_secret' => $fb_result->where('name','client_secret')->first()->value,
						'redirect' => url('/facebookAuthenticate'),
					],
				]);

				$fb = new FacebookHelper;
				View::share('fb_url', $fb->getUrlLogin());
				define('FB_URL', $fb->getUrlLogin());
			}
			if(Schema::hasTable('admin')) {
				$admin_email = Admin::first()->email;
				View::share('admin_email', $admin_email);
			}

			if (Schema::hasTable('payment_gateway')) {
				$this->setPaymentConfig();
			}

			// Configure Email settings from email_settings table
			if(Schema::hasTable('email_settings'))
	        {
	            $result = DB::table('email_settings')->get();

	            Config::set([
                    'mail.driver' => email_settings('driver'),
                    'mail.host' 		=> email_settings('host'),
                    'mail.port'       	=> email_settings('port'),
                    'mail.encryption' 	=> email_settings('encryption'),
                    'mail.username'   	=> email_settings('username'),
                    'mail.password'   	=> email_settings('password'),
                    'mail.from' => [
                    	'address' => email_settings('from_address'),
                    	'name'    => email_settings('from_name')
                    ],
                ]);

	            if(email_settings('driver') == 'mailgun') {
		            Config::set([
	                    'services.mailgun.domain'     => email_settings('domain'),
	                    'services.mailgun.secret'     => email_settings('secret'),
	                ]);
	           	}

	            Config::set([
                    'laravel-backup.notifications.mail.from' => email_settings('from_address'),
                    'laravel-backup.notifications.mail.to'   => email_settings('from_address'),
	            ]);
	        }

	        if(Schema::hasTable('language')) {

	        	// Language lists for footer
			        $language = Language::pluck('name', 'value');
			        View::share('language', $language);
					
					// Default Language for footer
					$default_language = Language::where('default_language', '=', '1')->first();
					View::share('default_language', $default_language);

			        if($default_language) {

						Session::put('language', $default_language->value);
						App::setLocale($default_language->value);


					}

	        }
		}

		// Enable pagination
	    if (!Collection::hasMacro('paginate')) {
	    	Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
	            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

	            return new LengthAwarePaginator($this->forPage($page, $perPage), $total ?: $this->count(), $perPage, $page, [
	                'path' => LengthAwarePaginator::resolveCurrentPath(),
	                'pageName' => $pageName,
	            ]);
	        });
	    }

	    // Append Array to laravel Collection through map
	    if (!Collection::hasMacro('setAppends')) {
	    	Collection::macro('setAppends', function ($attributes) {
			    return $this->map(function ($item) use ($attributes) {
			        return $item->setAppends($attributes);
			    });
			});
	    }

	    // Append Array to laravel Collection through transform
	    if (!Collection::hasMacro('transformWithAppends')) {
	    	Collection::macro('transformWithAppends', function ($attributes) {
	    		return $this->transform(function ($item) use ($attributes) {
	    			foreach ($attributes as $attribute) {
	    				$item[$attribute] = $item->$attribute;
	    			}
	    			return $item;
			    });
			});
	    }

	    // Custom Validation for File Extension
        \Validator::extend('valid_extensions', function($attribute, $value, $parameters) 
        {
            if(count($parameters) == 0) {
                return false;
            }
            $ext = strtolower($value->getClientOriginalExtension());
            
            return in_array($ext,$parameters);
        });
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		foreach(glob(app_path() . '/Helpers/*.php') as $file) {
            require_once $file;
        }

        foreach(glob(app_path() . '/Constants/*.php') as $file) {
            require_once $file;
        }
	}

	protected function bindModels()
	{
		if (Schema::hasTable('site_settings')) {
			$this->app->singleton('site_settings', function ($app) {
	            $site_settings = SiteSettings::get();
	            return $site_settings;
	        });
		}

		if (Schema::hasTable('email_settings')) {
			$this->app->singleton('email_settings', function ($app) {
	            $email_settings = EmailSettings::get();
	            return $email_settings;
	        });
		}

		if (Schema::hasTable('api_credentials')) {
			$this->app->singleton('api_credentials', function ($app) {
	            $api_credentials = ApiCredentials::get();
	            return $api_credentials;
	        });
		}

		if (Schema::hasTable('payment_gateway')) {
			$this->app->singleton('payment_gateway', function ($app) {
	            $payment_gateway = PaymentGateway::get();
	            return $payment_gateway;
	        });
		}

		if (Schema::hasTable('referral_settings')) {
			$this->app->singleton('referral_settings', function ($app) {
	            $referral_settings = ReferralSetting::get();
	            return $referral_settings;
	        });
		}

		if (Schema::hasTable('fees')) {
			$this->app->singleton('fees', function ($app) {
	            $fees = Fees::get();
	            return $fees;
	        });
		}

		$this->app->bind('App\Contracts\ImageHandlerInterface','App\Services\LocalImageHandler');
	}

	protected function shareCommonData()
	{
		$acceptable_mimes = array(
			'image/jpeg',
			'image/jpg',
			'image/gif',
			'image/png',
		);

		View::share('acceptable_mimes',$acceptable_mimes);
	}

	protected function setPaymentConfig()
	{
		$paypal_mode  = payment_gateway('mode','Paypal');
		
		define('PAYPAL_ID', payment_gateway('mode','paypal_id'));
		define('PAYPAL_MODE', ($paypal_mode == 'sandbox') ? 0 : 1);
		define('PAYPAL_CLIENT_ID', payment_gateway('client','Paypal'));

		define('STRIPE_KEY', payment_gateway('publish','Stripe'));
		define('STRIPE_SECRET', payment_gateway('secret','Stripe'));

		$site_settings = resolve('site_settings');

		$this->app->bind('braintree', function($app) {
			$bt_env = payment_gateway('mode','Braintree');
			$bt_merchant_id = payment_gateway('merchant_id','Braintree');
			$bt_public_key = payment_gateway('public_key','Braintree');
			$bt_private_key = payment_gateway('private_key','Braintree');
            $config = new \Braintree_Configuration([
			    'environment' => $bt_env,
			    'merchantId' => $bt_merchant_id,
			    'publicKey' => $bt_public_key,
			    'privateKey' => $bt_private_key,
			]);

			return new \Braintree_Gateway($config);
        });

        $this->app->bind('paypal', function($app) {
        	$gateway = \Omnipay\Omnipay::create('PayPal_Rest');

			$gateway->initialize(array(
				'clientId' 	=> payment_gateway('client','Paypal'),
				'secret' 	=> payment_gateway('secret','Paypal'),
				'testMode' 	=> (payment_gateway('mode','Paypal') == 'sandbox'),
			));

			return $gateway;
        });
	}
}
