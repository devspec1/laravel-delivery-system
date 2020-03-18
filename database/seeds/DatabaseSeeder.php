<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $this->call(SiteSettingsTableSeeder::class);
        $this->call(JoinUsTableSeeder::class);
        $this->call(ApiCredentialsTableSeeder::class);
        $this->call(EmailSettingsTableSeeder::class);
        $this->call(ReferralSettingsTableSeeder::class);
        $this->call(FeesTableSeeder::class);
        $this->call(TollReasonSeeder::class);

        $this->call(MetasTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(CountryTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(PaymentGatewayTableSeeder::class);
        $this->call(PagesTableSeeder::class);

        $this->call(VehiclesTableSeeder::class);
        $this->call(LaravelEntrustSeeder::class);
        $this->call(CompaniesTableSeeder::class);
    }
}