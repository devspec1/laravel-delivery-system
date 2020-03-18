<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Closure;
use JWTAuth;
use App;
use Auth;

class JwtMiddleware extends BaseMiddleware
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle($request, Closure $next)
	{
		$validate_token = $this->validateToken($request);

		if($validate_token) {
			return $validate_token;
		}

		return $next($request);
	}

	protected function validateToken($request)
	{
		try {
			$user_details = JWTAuth::parseToken()->authenticate();
			if($user_details == '') {
				return response()->json(['status' => 'Token is Invalid'],401);
			}

			if(!$user_details) {
				return response()->json(['status' => 'user_not_found']);
			}

			if($user_details->status == 'Inactive') {
				return response()->json(['status' => 'Inactive User'],401);
			}
			Auth::setUser($user_details);

			if ($user_details && @$user_details->language !== null) {
				session(['language' => $user_details->language]);
				App::setLocale($user_details->language);
			}
			else if(isset($request->language)) {
				session(['language' => $request->language]);
				App::setLocale($request->language);
			}
			else {
				App::setLocale('en');
			}
		}
		catch (\Exception $e) {
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
				return response()->json(['status' => 'Token is Invalid'],401);
			}
			else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
				return $this->getRefreshToken($request->token);
			}
			else {
				return response()->json(['status' => 'Authorization Token not found'],401);
			}
		}
		return false;
	}

	protected function getRefreshToken($token)
	{
		try {
			$refreshed = JWTAuth::refresh($token);
		}
		catch (\Exception $e) {
			return response()->json(['status' => 'Token is Invalid'],401);
		}

		return response()->json([
			'status_message' 	=> "Token Expired",
			'status_code' 		=> "0",
			'refresh_token' 	=> $refreshed,
		]);
	}
}