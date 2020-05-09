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
			['id' => '1', 'name' => 'Gloria Food', 'description' => 'Integration with Gloria Food'],
		]);
    }
}
