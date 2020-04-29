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
			['id' => '1', 'plan_id' => 'plan_GQJRSjXx14TuLc', 'plan_name' => 'Regular'],
			['id' => '2', 'plan_id' => 'plan_GQJPw5BNB3TXTP', 'plan_name' => 'Founder'],
		]);
    }
}