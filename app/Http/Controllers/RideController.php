<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;

class RideController extends BaseController
{
	     public function safety(){
        return view('ride.safety');
    }
     public function ride(){
        return view('ride.ride');
    }
     public function how_it_works(){
        return view('ride.how_it_works');
    }
}