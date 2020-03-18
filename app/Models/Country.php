<?php

/**
 * Country Model
 *
 * @package     Gofer
 * @subpackage  Model
 * @category    Country
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'country';

    public $timestamps = false;

    public function scopeCodeSelect($query)
    {
        return $query->select('long_name','phone_code')->get();
    }

  // get Iban required country in stripe
    public static function getIbanRequiredCountries() 
    {
        $iban_required_countries = ['DK', 'FI', 'FR', 'DE', 'GI', 'IE', 'IT', 'LU', 'NL', 'NO', 'PT', 'ES', 'SE', 'CH', 'AT', 'BE'];
        return $iban_required_countries;
    }
    // get branch code required country in stripe
    public static function getBranchCodeRequiredCountries() 
    {
        $iban_required_countries = ['HK', 'CA', 'JP', 'SG'];
        return $iban_required_countries;
    }
    public static function getCurrency()
    {
        $currency = [];
        $currency['AT'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['AU'] = ['AUD'];
        $currency['BE'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['CA'] = ['CAD', 'USD'];
        $currency['GB'] = ['GBP', 'EUR', 'DKK', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['HK'] = ['HKD'];
        $currency['JP'] = ['JPY'];
        $currency['NZ'] = ['NZD'];
        $currency['SG'] = ['SGD'];
        $currency['US'] = ['USD'];
        $currency['CH'] = ['CHF', 'EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD'];
        $currency['DE'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['DK'] = ['DKK', 'EUR', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['ES'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['FI'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['FR'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['IE'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['IT'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['LU'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['NL'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['NO'] = ['NOK', 'EUR', 'DKK', 'GBP', 'SEK', 'USD', 'CHF'];
        $currency['PT'] = ['EUR', 'DKK', 'GBP', 'NOK', 'SEK', 'USD', 'CHF'];
        $currency['SE'] = ['SEK', 'EUR', 'DKK', 'GBP', 'NOK', 'USD', 'CHF'];

        return $currency;

    }
    // stripe supported country
    public static function getPayoutCoutries()
    {
        $payout_countries = array(
            'AT' => 'Austria',
            'AU' => 'Australia',
            'BE' => 'Belgium',
            'CA' => 'Canada',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'FR' => 'France',
            'DE' => 'Germany',
            'HK' => 'Hong Kong',
            'IE' => 'Ireland',
            'IT' => 'Italy',
            'JP' => 'Japan',
            'LU' => 'Luxembourg',
            'NL' => 'Netherlands',
            'NZ' => 'New Zealand',
            'NO' => 'Norway',
            'PT' => 'Portugal',
            'SG' => 'Singapore',
            'ES' => 'Spain',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'OT' => 'Other',
        );
        return $payout_countries;
    }
}
