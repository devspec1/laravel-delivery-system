<?php

use Illuminate\Database\Seeder;

class PaymentGatewayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_gateway')->delete();

        DB::table('payment_gateway')->insert([
            ['name' => 'trip_default', 'value' => 'cash', 'site' => 'Common'],
            ['name' => 'payout_methods', 'value' => 'bank_transfer,paypal,stripe', 'site' => 'Common'],
            ['name' => 'is_enabled', 'value' => '1', 'site' => 'Cash'],
            ['name' => 'is_enabled', 'value' => '1', 'site' => 'Paypal'],
            ['name' => 'paypal_id', 'value' => 'gofer@trioangle.com', 'site' => 'Paypal'],
            ['name' => 'mode', 'value' => 'sandbox', 'site' => 'Paypal'],
            ['name' => 'client', 'value' => 'AbZqxwGM87-fRHI-HnG_plBoz-Z_j2OgcAKRFQzgdR4qd5dszhQXS5nk6FTPd9sw0vSSLMadISBc2_lA', 'site' => 'Paypal'],
            ['name' => 'secret', 'value' => 'EDFYQf8itbqoWi-9BIzgzrNvGWLI62UEliT1i8f_APi_MAJkteZLwnXGmTvBkBIRAVy-jCBi-PmYyNUa', 'site' => 'Paypal'],
            ['name' => 'is_enabled', 'value' => '1', 'site' => 'Stripe'],
            ['name' => 'publish', 'value' => 'pk_test_lQctuc2tx2IVDCSYIjiFodaz00n0TNteiG', 'site' => 'Stripe'],
            ['name' => 'secret', 'value' => 'sk_test_1tiewAwj00VlKzL7uwMPZcTN003Vk0kWl6', 'site' => 'Stripe'],
            ['name' => 'api_version', 'value' => '2019-12-03', 'site' => 'Stripe'],
            ['name' => 'is_enabled', 'value' => '0', 'site' => 'Braintree'],
            ['name' => 'mode', 'value' => 'sandbox', 'site' => 'Braintree'],
            ['name' => 'merchant_id', 'value' => 'g3dprd7kyfs7f3jr', 'site' => 'Braintree'],
            ['name' => 'public_key', 'value' => 'prwd98qgnqkdptkp', 'site' => 'Braintree'],
            ['name' => 'private_key', 'value' => 'fe3e98760ba97b6b2e01fe28379cd477', 'site' => 'Braintree'],
        ]);
    }
}