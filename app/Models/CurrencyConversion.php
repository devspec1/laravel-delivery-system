<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use JWTAuth;

trait CurrencyConversion
{
	public $currency_code_field = 'currency_code';

	public $convert_currency_code;

	public $is_convert = true;

	public function __construct()
	{
		if(request()->segment(1) == 'admin') {
			$currency_code = Currency::defaultCurrency()->first()->code;
			$this->convert_currency_code =  $currency_code;
		}
		else {
			$this->convert_currency_code = $this->getSessionOrDefaultCode();
		}
	}

	public function getSessionOrDefaultCode()
	{
		if(request()->segment(1) == 'api') {
			if(request('token')) {
				try {
                	$currency_code = JWTAuth::toUser(request()->token)->currency_code;
				}
				catch(\Exception $e) {
					$currency_code = Currency::defaultCurrency()->first()->code;
				}
            }
            else {
                $currency_code = Currency::defaultCurrency()->first()->code;
            }
			if(!$currency_code) {
				$currency_code = $this->get_currency_from_ip(request()->ip_address);
			}
		}
		else {
			$currency_code = session()->get('currency');  
		}

		$currency_code = $this->CheckCurrency($currency_code);

		if(!$currency_code || $this->isAdminPanel()) {
			$currency_code = Currency::defaultCurrency()->first()->code;
		}
		return $currency_code;
	}

	public function CheckCurrency($currency_code)
	{
		$currency = Currency::where('code',$currency_code)->where('status',"Active")->first();

		if(!$currency) {
			$currency_code = Currency::defaultCurrency()->first()->code;
		}

		return $currency_code;
	}

	public function getConvertCurrencyCode()
	{
		return $this->convert_currency_code;
	}

	public function setConvertCurrencyCode($currency_code = '')
	{
		if($currency_code == '') {
			$currency_code = $this->getSessionOrDefaultCode();
		}
		$this->convert_currency_code = $currency_code;
		return $this;
	}

	public function disableAdminPanelConversion()
	{
		return @$this->disable_admin_panel_convertion;
	}

	public function isAdminPanel()
	{
		return request()->segment(1) == 'admin';
	}

	public function getIsConvert()
	{
		return $this->is_convert;
	}

	public function original()
	{
		$this->is_convert = false;
		return $this;
	}

	public function session()
	{
		$this->is_convert = true;
		return $this;
	}

	public function getCurrencyCodeField()
	{
		return $this->currency_code_field;
	}

	public function setCurrencyCodeField($currency_code_field)
	{
		$this->currency_code_field = $currency_code_field;
		return $this;     
	}

	public function isConvertableAttribute($attribute)
	{
		return in_array($attribute, $this->getConvertFileds());
	}

	public function getConvertFileds()
	{
		return $this->convert_fields ?: array();
	}

	/**
	* @return array
	*/
	public function attributesToArray()
	{
		$attributes = parent::attributesToArray();
		if ($this->canConvert()) {
			foreach($this->convert_fields as $field) {
				$attributes[$field] = $this->getAttribute($field);
			}
			$attributes['currency_code'] = $this->getToCurrencyCode();
		}

		return $attributes;
	}

	protected function getArrayableAppends()
	{
		$this->appends = array_unique(array_merge($this->appends, ['currency_symbol', 'original_currency_code']));

		return parent::getArrayableAppends();
	}

	public function canConvert()
	{
		return ($this->getIsConvert() && (!$this->isAdminPanel() || !$this->disableAdminPanelConversion()));
  	}

	public function getAttribute($attribute)
	{
		if($this->canConvert()) {
			if ($this->isConvertableAttribute($attribute)) {
				$value = parent::getAttribute($attribute);
				$converted_value = $this->getConvertedValue($value);
				return $converted_value;
			}

			if($attribute == 'currency_code') {
				return $this->getToCurrencyCode();
			}
		}
		return parent::getAttribute($attribute);
	}

	public function getSessionCurrencyAttribute()
	{
		return Currency::whereCode($this->getSessionOrDefaultCode())->first();
	}

	public function getCurrencySymbolAttribute()
	{
		if($this->getSessionCurrencyAttribute()) {
			return $this->getSessionCurrencyAttribute()->symbol;
		}
		return '$';
	}

	public function getOriginalCurrencyCodeAttribute()
	{
		return $this->getOriginal('currency_code');
	}

	public function getFromCurrencyCode()
	{
		$field = $this->getCurrencyCodeField();
		return parent::getAttribute($field) ?: '';
	}

	public function getToCurrencyCode()
	{
		$code = $this->getConvertCurrencyCode();
		return $code;
	}

	public function getConvertedValue($price)
	{
		$from = $this->getFromCurrencyCode();
		$to = $this->getToCurrencyCode();
		$converted_price = $this->currency_convert($from, $to, $price);
		return $converted_price;
	}

	/**
	* Currency Convert
	*
	* @param int $from   Currency Code From
	* @param int $to     Currency Code To
	* @param int $price  Price Amount
	* @return int Converted amount
	*/
	public function currency_convert($from = '', $to = '', $price = 0)
	{
		if($from == '') {
			$from = $this->getSessionOrDefaultCode();
		}
		if($to == '') {
			$to = $this->getSessionOrDefaultCode();
		}

		$rate = Currency::whereCode($from)->first()->rate;
		$session_rate = Currency::whereCode($to)->first();

		if($session_rate) {
			$session_rate = $session_rate->rate;
		}
		else {
			$session_rate = '1';
		}

		if($rate!="0.0") {
			if($price) {
				$usd_amount = floatval($price) / floatval($rate);
			}
			else {
				$usd_amount = 0;
			}

		}
		else {
			echo "Error Message : Currency value '0' (". $from . ')';
			die;
		}
		
		return number_format($usd_amount * $session_rate, 2, '.', '');
	}

	/**
	* Get Currency code from IP address
	* @param $ip_address 
	* @return $currency_code
	*/
	public function get_currency_from_ip($ip_address = '')
	{
		$ip_address = $ip_address ?: request()->getClientIp();
		$default_currency = Currency::active()->defaultCurrency()->first();
		$currency_code    = @$default_currency->code;
		if(session()->get('currency_code')) {
			$currency_code = session()->get('currency_code');
		}
		else if($ip_address!='') {
			$result = array();
			try {
				$result = unserialize(file_get_contents_curl('http://www.geoplugin.net/php.gp?ip='.$ip_address));
			}
			catch(\Exception $e) {
			}
	        // Default Currency code for footer
			if(@$result['geoplugin_currencyCode']) {
				$currency_code =  $result['geoplugin_currencyCode'];
			}
			session()->put('currency_code', $currency_code);
		}
		return $currency_code;
	}
}