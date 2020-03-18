<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;

class DriveController extends BaseController
{
	     public function drive(){
        return view('drive.drive');
    }
    	   public function requirements(){
        return view('drive.requirements');
    }
    	   public function driver_app(){
        return view('drive.driver_app');
    }
    	   public function drive_safety(){
        return view('drive.drive_safety');
    }
   
}