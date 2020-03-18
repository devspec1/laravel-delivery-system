<?php

/**
 * Facebook Helper
 *
 * @package     Gofer
 * @subpackage  Helper
 * @category    Helper
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Helper;
 
use Facebook\Facebook;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Config;

session_start();

class FacebookHelper
{
	private $helper;	// Global variable for Facebok Helper
	private $session;	// Global variable for Session
	private $fb;		// Global variable for Facebook Library instance
	
	/**
     * Constructor to Set Facebook instance in Global variable
     */
	public function __construct()
	{
		$this->fb = new Facebook([
  							'app_id' => Config::get('facebook.client_id'),
  							'app_secret' => Config::get('facebook.client_secret'),
  							'default_graph_version' => 'v2.2',
  							]);

		$this->helper = $this->fb->getRedirectLoginHelper();
	}

	

	/**
     * Get a Facebook Connect URL
     *
     * @return URL from Facebook API
     */
	public function getUrlConnect()
	{
		return $this->helper->getLoginUrl(url('facebookConnect'), ['public_profile','email', 'user_friends']);
	}

	/**
     * Get a Facebook User Access Token
     *
     * @return Store Access Token in Session
     */
	public function generateSessionFromRedirect()
	{
		$this->session = null;

		try {
		  $accessToken = $this->helper->getAccessToken();
		} catch(FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
		
		if (isset($accessToken)) {
		  // Logged in!
		  $_SESSION['facebook_access_token'] = (string) $accessToken;
		  // Now you can redirect to another page and use the
		  // access token from $_SESSION['facebook_access_token']
		}
	}

	/**
     * Get a Facebook User Data
     *
     * @return response from Facebook API
     */
	public function getData()
	{
		// Sets the default fallback access token so we don't have to pass it to each request
		if(@$_SESSION['facebook_access_token'])
		{ 
		$this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
		
		try {
		  // $response = $this->fb->get('/1231320426892210?fields=id,first_name,last_name,friends,email,context.fields(all_mutual_friends)');
		  $response = $this->fb->get('/me?fields=id,first_name,last_name,friends,email');
		  //$userNode = $response->getGraphUser();
		} catch(FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
		return $response;
		}
		return 'Failed';
	}
	/**
     * Get a Facebook Login URL
     *
     * @return URL from Facebook API
     */
	public function getUrlLogin()
	{
		return $this->helper->getLoginUrl(url('facebookAuthenticate'), ['public_profile','email', 'user_friends']);
	}
}
