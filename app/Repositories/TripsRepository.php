<?php 

/**
 * Repository
 *
 * @package     Gofer
 * @subpackage  Repository
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\Trips;
use DB;

class TripsRepository
{

    public function heatMapData()
    {
        $trips = DB::table('trips')->select('pickup_latitude','pickup_longitude', DB::raw('count(*) as weight'))->groupBy('pickup_latitude','pickup_longitude')->get();
        return $trips;      
    }
    
}