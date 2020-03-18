<?php

/**
 * Payout Preference Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Payout Preference
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeZone;
use DateTime;
use Config;

class PayoutPreference extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'payout_preference';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'payout_method'];

	public $appends = ['updated_time', 'updated_date','account_holder_name'];

	// Join with users table
	public function users()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	// Get Updated time for Payout Information
	public function getUpdatedTimeAttribute()
	{
		if (request()->segment(1) == 'api') {
			$date = PHP_DATE_FORMAT;
			$datemonth = date($date, strtotime($this->attributes['updated_at']));
			return $datemonth . ' at ' . date('H:i', strtotime($date));
		}

		$new_str = new DateTime($this->attributes['updated_at'], new DateTimeZone(Config::get('app.timezone')));

       	$new_str->setTimeZone(new DateTimeZone('Asia/Kolkata'));

       	$datemonth = date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at']));
       	return $datemonth;
	}

	// Get Updated date for Payout Information
	public function getUpdatedDateAttribute()
	{
		if (request()->segment(1) == 'api') {
			$date = PHP_DATE_FORMAT;
			$datemonth = date($date, strtotime($this->attributes['updated_at']));
			return $datemonth;
		}

		$new_str = new DateTime($this->attributes['updated_at'], new DateTimeZone(Config::get('app.timezone')));

        $new_str->setTimeZone(new DateTimeZone('Asia/Kolkata'));

        $datemonth = date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at']));
        return $datemonth;
	}

	public function getDocumentAttribute()
	{
		$image_name = $this->attributes['document_image'];
		$user_id = $this->attributes['user_id'];
		$url = url('images/users/' . $user_id . '/uploads/' . $image_name);
		return $url;
	}

	//account_holder_name
	public function getAccountHolderNameAttribute()
	{
		return  $this->attributes['holder_name'];
	}

	// get mandatory field for create stripe token
	public static function getMandatory($country = 'US')
	{
		$mandatory = [];
		$mandatory['AT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['AU'] = array('bsb' => 'required','account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['BE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['CA'] = array('transit_number' => 'required','account_number' => 'required','institution_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['GB'] = array('sort_code' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['HK'] = array('clearing_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['JP'] = array('bank_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'bank_name' => 'required', 'branch_name' => 'required', 'account_owner_name' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['NZ'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['SG'] = array('bank_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['US'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required', 'ssn_last_4' => 'required');
		$mandatory['CH'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['DE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['DK'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['ES'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['FI'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['FR'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['IE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['IT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['LU'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['NL'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['NO'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['PT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['SE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['OT'] = array('account_number' => 'required', 'bank_name' => 'required', '', 'account_holder_name' => 'required','branch_name' => 'required');

		return @$mandatory[$country] ? @$mandatory[$country] : NULL;
	}

	// get mandatory field for create stripe token
	public static function getMandatoryField()
	{
		$mandatory['AU'] = array('bsb' => 'required','account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required', 'routing_number' => 'required');
		$mandatory['CA'] = array('transit_number' => 'required','account_number' => 'required','institution_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required', 'routing_number' => 'required');
		$mandatory['GB'] = array('sort_code' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['HK'] = array('clearing_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['JP'] = array('bank_code' => 'required','bank_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'bank_name' => 'required', 'branch_name' => 'required', 'account_owner_name' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['NZ'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['SG'] = array('bank_code' => 'required', 'account_number' => 'required', 'branch_code' => 'required', 'account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['US'] = array('routing_number' => 'required', 'account_number' => 'required', 'account_holder_name' => 'required', 'currency' => 'required', 'ssn_last_4' => 'required');
		$mandatory['AT'] = array('iban' => 'required','account_number'=>'required','account_holder_name' => 'required', 'currency' => 'required');
		$mandatory['BE'] = array('iban' => 'required','account_holder_name' => 'required','currency' => 'required','account_number'=>'required');
		$mandatory['CH'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['DE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number' => 'required');
		$mandatory['DK'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number' => 'required');
		$mandatory['ES'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['FI'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number' => 'required');
		$mandatory['FR'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number' => 'required');
		$mandatory['IE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['IT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['LU'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['NL'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['NO'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['PT'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['SE'] = array('iban' => 'required', 'account_holder_name' => 'required', 'currency' => 'required','account_number'=>'required');
		$mandatory['OT'] = array('account_number' => 'required', 'bank_name' => 'required', 'account_holder_name' => 'required','branch_name' => 'required');

		return $mandatory;
	}


	// get mandatory field for create stripe token
	public static function getAllMandatory()
	{
		$mandatory = [];
		$mandatory['AT'] = array('IBAN');
		$mandatory['AU'] = array('BSB', 'Account Number');
		$mandatory['BE'] = array('IBAN');
		$mandatory['CA'] = array('Transit Number', 'Account Number', 'Institution Number');
		$mandatory['GB'] = array('Sort Code', 'Account Number');
		$mandatory['HK'] = array('Clearing Code', 'Account Number', 'Branch Code');
		$mandatory['JP'] = array('Bank Code', 'Account Number', 'Branch Code', 'Bank Name', 'Branch Name', 'Account Owner Name ');
		$mandatory['NZ'] = array('Routing Number', 'Account Number');
		$mandatory['SG'] = array('Bank Code', 'Account Number', 'Branch Code');
		$mandatory['US'] = array('Routing Number', 'Account Number');
		$mandatory['CH'] = array('IBAN');
		$mandatory['DE'] = array('IBAN');
		$mandatory['DK'] = array('IBAN');
		$mandatory['ES'] = array('IBAN');
		$mandatory['FI'] = array('IBAN');
		$mandatory['FR'] = array('IBAN');
		$mandatory['IE'] = array('IBAN');
		$mandatory['IT'] = array('IBAN');
		$mandatory['LU'] = array('IBAN');
		$mandatory['NL'] = array('IBAN');
		$mandatory['NO'] = array('IBAN');
		$mandatory['PT'] = array('IBAN');
		$mandatory['SE'] = array('IBAN');
		$mandatory['OT'] = array('Account Holder Name', 'Account Number','','Bank Name','Branch Name');
		return $mandatory;
	}
}