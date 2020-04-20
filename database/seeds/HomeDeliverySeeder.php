<?php

use Illuminate\Database\Seeder;

class HomeDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_orders')->delete();

		DB::table('delivery_orders')->insert([
            ['id' => '1', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '1.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Funky, Thai, Keilor', 'latitude' => '5', 'longitude' => '2'],
            ['id' => '2', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '2.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Second, Thai, Second', 'latitude' => '5', 'longitude' => '1'],
            ['id' => '3', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '3 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Third, Second, Thai', 'latitude' => '5', 'longitude' => '4'],
            ['id' => '4', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '2.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Funky, Thai, Keilor', 'latitude' => '5', 'longitude' => '3'],
            ['id' => '5', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '1.6 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Third, Second, Thai', 'latitude' => '5', 'longitude' => '5'],
            ['id' => '6', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '2.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Funky, Thai, Keilor', 'latitude' => '5', 'longitude' => '7'],
            ['id' => '7', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '3.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Second, Thai, Second', 'latitude' => '5', 'longitude' => '6'],
            ['id' => '8', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '4.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Funky, Thai, Keilor', 'latitude' => '5', 'longitude' => '8'],
            ['id' => '9', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '4.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Third, Second, Thai', 'latitude' => '5', 'longitude' => '9'],
            ['id' => '10', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '3 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Funky, Thai, Keilor', 'latitude' => '5', 'longitude' => '10'],
            ['id' => '11', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '5.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Funky, Thai, Keilor', 'latitude' => '5', 'longitude' => '12'],
            ['id' => '12', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '5.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Third, Second, Thai', 'latitude' => '5', 'longitude' => '15'],
            ['id' => '13', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '4.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Second, Thai, Second', 'latitude' => '5', 'longitude' => '14'],
            ['id' => '14', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '6.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Third, Second, Thai', 'latitude' => '5', 'longitude' => '13'],
            ['id' => '15', 'driver_id' => null, 'status' => 'new', 'estimate_time' => '5.5 hours', 'fee' => 30.00, 'currency_code' => 'AUD', 'pick_up' => 'Third, Second, Thai', 'latitude' => '5', 'longitude' => '11'],
		]);
    }
}
