<?php

/**
 * User Observer
 *
 * @package     Gofer
 * @subpackage  Observer
 * @category    User
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Observers;

use App\Http\Helper\RequestHelper;
use App\Models\User;
use App\Models\Currency;
use App\Repositories\DriverOweAmountRepository;

class UserObserver
{
    protected $request_helper; // Global variable for Helpers instance

    public function __construct(RequestHelper $request,DriverOweAmountRepository $driver_owe_amt_repository)
    {
        $this->request_helper = $request;
        $this->driver_owe_amt_repository = $driver_owe_amt_repository;
    }

    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        if($user->user_type == 'Driver' || $user->user_type == 'driver') {
            $currency_code = $user->currency_code;
            if ($user->currency_code == null) {
                $default_currency = Currency::active()->defaultCurrency()->first();
                $currency_code = @$default_currency->code;
            }

            $this->driver_owe_amt_repository->create([
                'user_id' => $user->id,
                'amount' => 0,
                'currency_code' => $currency_code,
            ]);
        }
    }

}