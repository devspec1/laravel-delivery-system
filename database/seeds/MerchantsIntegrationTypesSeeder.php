<?php

use Illuminate\Database\Seeder;

class MerchantsIntegrationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('merchants_integration_types')->delete();

		DB::table('merchants_integration_types')->insert([
			['id' => '1', 'name' => 'Gloria Food', 'description' => 'Integration with Gloria Food', 'delivery_fee' => '8.95', 'delivery_fee_per_km' => '1.00', 'delivery_fee_base_distance' => 5.0],
		]);
    }
}
