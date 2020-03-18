<?php

use Illuminate\Database\Seeder;

class VehiclesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_type')->insert(['id'=>'1','car_name' =>'GoferGo','description' =>'GoferGo','status' =>'Active','vehicle_image'=> 'gofergo.png','active_image' =>'gofergo.png']);
        DB::table('car_type')->insert(['id'=>'2','car_name' =>'GoferX','description' =>'GoferX','status' =>'Active','vehicle_image'=> 'goferx.png','active_image' =>'goferx.png']);
        DB::table('car_type')->insert(['id'=>'3','car_name' =>'GoferXL','description' =>'GoferXL','status' =>'Active','vehicle_image'=> 'goferxl.png','active_image' =>'goferxl.png']);
    }   
}