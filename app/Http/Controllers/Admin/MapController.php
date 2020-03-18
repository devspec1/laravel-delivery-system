<?php

/**
 * Map Type Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Map
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\CarTypeDataTable;
use App\Models\User;
use App\Models\DriverLocation;
use App\Models\DriverDocuments;
use App\Http\Start\Helpers;
use App\Repositories\TripsRepository;
use Validator;
use DB;
use Auth;

class MapController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for car Type
     *
     * @param array $dataTable  Instance of CarTypeDataTable
     * @return datatable
     */
    public function index(Request $request)
    {
        return view('admin.map');        
    }

    public function mapdata(Request $request){
        $mapdata    = User::with([
            'profile_picture' => function($query){},
            'driver_location' => function($query){},
            'rider_location' => function($query){},
        ])
        ->where('status','!=','null')
        ->where('device_type', '!=', NUll)
        ->where('device_id', '!=' , '')
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') {
                //For company login, only get that company's driver
                $query->where('company_id',Auth::guard('company')->user()->id)
                ->where('user_type','!=','Rider');
            }
        });


        $mapdata = $mapdata->get()->toJson();
        $mapdata = json_decode($mapdata, true);
        echo json_encode($mapdata);
    }
    public function heat_map()
    {
        return view('admin.heat_map');        
    }
    public function heat_map_data(TripsRepository $trip_repository)
    {
        $trips = $trip_repository->heatMapData();
        return json_encode($trips);      
    }

}
