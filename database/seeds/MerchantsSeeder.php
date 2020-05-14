<?php

use Illuminate\Database\Seeder;

class MerchantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('merchants')->delete();

		DB::table('merchants')->insert([
			['id' => '1', 'name' => 'Default merchant', 'description' => 'Default merchant for manual orders', 'integration_type' => '1',],
        ]);
    }
}
