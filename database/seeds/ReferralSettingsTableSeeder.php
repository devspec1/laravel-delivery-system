<?php

use Illuminate\Database\Seeder;

class ReferralSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('referral_settings')->delete();

        DB::table('referral_settings')->insert([
            ['name' => 'apply_referral',    'value' => '1', 'user_type' => 'Driver'],
            ['name' => 'number_of_trips',   'value' => '5',  'user_type' => 'Driver'],
            ['name' => 'number_of_days',    'value' => '3',  'user_type' => 'Driver'],
            ['name' => 'currency_code',     'value' => 'USD',  'user_type' => 'Driver'],
            ['name' => 'referral_amount',   'value' => '10',  'user_type' => 'Driver'],
            ['name' => 'apply_referral',    'value' => '1', 'user_type' => 'Rider'],
            ['name' => 'number_of_trips',   'value' => '5',  'user_type' => 'Rider'],
            ['name' => 'number_of_days',    'value' => '3',  'user_type' => 'Rider'],
            ['name' => 'currency_code',     'value' => 'USD',  'user_type' => 'Rider'],
            ['name' => 'referral_amount',   'value' => '10',  'user_type' => 'Rider'],
        ]);
    }
}