<?php

use Illuminate\Database\Seeder;

class FeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fees')->delete();

        DB::table('fees')->insert([
        		['name' => 'access_fee', 'value' => 10],
                ['name' => 'driver_peak_fare', 'value' => '70'],
                ['name' => 'driver_access_fee', 'value' => '10'],
                ['name' => 'additional_fee', 'value' => 'Yes'],
                
        	]);
    }
}
