<?php 

/**
 * Driver Owe Amount Repository
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver Owe Amount
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\DriverOweAmount;
use App\Models\User;
use App\Http\Start\Helpers;
use DB;

class DriverOweAmountRepository
{

    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(DriverOweAmount $driver_owe_amount)
    {
        $this->model = $driver_owe_amount;
        $this->helper = new Helpers;
    }

    // create a new record in the database
    public function create(array $data)
    {
        $driver_owe_amount = new DriverOweAmount();
        $driver_owe_amount->user_id = $data['user_id'];
        $driver_owe_amount->amount = $data['amount'];
        $driver_owe_amount->currency_code = $data['currency_code'];
        $driver_owe_amount->save();
    }

    // update record in the database
    public function update($driver_id,$remaining_owe_amount,$from_currency_code)
    {
        $driver = User::find($driver_id);
        $to_currency_code = $driver->currency_code;
        $remaining_owe_amount = $this->helper->currency_convert($from_currency_code,$to_currency_code,$remaining_owe_amount);
        $driver_owe_amount = DriverOweAmount::where('user_id',$driver_id)
            ->update([
                'amount' => $remaining_owe_amount,
                'currency_code' => $to_currency_code,
                'user_id' => $driver_id,
            ]);
    }
}