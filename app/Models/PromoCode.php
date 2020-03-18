<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use CurrencyConversion;

    protected $table = 'promo_code';

    protected $appends = ['expire_date_dmy','expire_date_mdy','original_amount'];

    protected $convert_fields = ['amount', 'original_amount'];
    public $disable_admin_panel_convertion = true;

    public function getExpireDateDmyAttribute()
    {
    	return date('d-m-Y',strtotime($this->attributes['expire_date']));
    }
    public function getExpireDateMdyAttribute()
    {
        return date('m/d/Y',strtotime($this->attributes['expire_date']));
    }

    public function getAmountAttribute()
    {
    	return number_format(($this->attributes['amount']),2 ,'.', '');
    }
    public function getOriginalAmountAttribute()
    {
        return $this->attributes['amount'];
    }
}
