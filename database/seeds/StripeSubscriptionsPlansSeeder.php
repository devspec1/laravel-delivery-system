<?php

use Illuminate\Database\Seeder;

class StripeSubscriptionsPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run()
    {
        DB::table('stripe_subscription_plans')->delete();

		DB::table('stripe_subscription_plans')->insert([
			['id' => '1', 'plan_id' => 'plan_none', 'plan_name' => 'Driver Only'],
            ['id' => '2', 'plan_id' => 'prod_GyPB2FCdoejCk6', 'plan_name' => 'Member Driver'],
            ['id' => '3', 'plan_id' => 'plan_HF8wOsR0eoBLGw', 'plan_name' => 'Regular'],
            ['id' => '4', 'plan_id' => 'plan_HF8wOsR0eoBLGw', 'plan_name' => 'Founder'],
            ['id' => '5', 'plan_id' => 'plan_HF8wOsR0eoBLGw', 'plan_name' => 'Executive'],
		]);
    }
}