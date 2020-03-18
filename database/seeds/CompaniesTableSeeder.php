<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('companies')->delete();

        DB::table('companies')->insert([
        	['id'=>'1','name'=>'Admin', 'email'=>'admin@trioangle.com', 'country_code'=>'91','mobile_number'=>'9876543210','password'=>Hash::make('123456'),'status'=>'Active', 'company_commission' => 0,'created_at' => '2016-04-17 00:00:00']
        ]);
    }
}
