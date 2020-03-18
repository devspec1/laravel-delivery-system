<?php

use Illuminate\Database\Seeder;

class MetasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metas')->delete();

        DB::table('metas')->insert([
                ['url' => '/', 'title' => 'Home Page', 'description' => 'Home Page', 'keywords' => ''],
                ['url' => 'signin', 'title' => 'Sign In', 'description' => 'Sign In', 'keywords' => ''],
                ['url' => 'signin_driver', 'title' => 'Sign In Driver', 'description' => 'Sign In Driver', 'keywords' => ''],
                ['url' => 'signin_rider', 'title' => 'Sign In Rider', 'description' => 'Sign In Rider', 'keywords' => ''],
                ['url' => 'signup', 'title' => 'Sing Up', 'description' => 'Sing Up', 'keywords' => ''],
                ['url' => 'signup_driver', 'title' => 'Sign Up Driver', 'description' => 'Sign Up Driver', 'keywords' => ''],
                ['url' => 'signup_rider', 'title' => 'Sign Up Rider', 'description' => 'Sign Up Rider', 'keywords' => ''],
                ['url' => 'ride', 'title' => 'Rider Home Page', 'description' => 'Rider Home Page', 'keywords' => ''],
                ['url' => 'drive', 'title' => 'Driver Home Page', 'description' => 'Driver Home Page', 'keywords' => ''],
                ['url' => 'safety', 'title' => 'Trip safety', 'description' => 'Trip safety', 'keywords' => ''],
                ['url' => 'how_it_works', 'title' => 'How its works', 'description' => 'How its works', 'keywords' => ''],
                ['url' => 'requirements', 'title' => 'Driver requirements', 'description' => 'Driver requirements', 'keywords' => ''],
                ['url' => 'driver_app', 'title' => 'Driver App', 'description' => 'Driver App', 'keywords' => ''],
                ['url' => 'drive_safety', 'title' => 'Driver Safety', 'description' => 'Driver Safety', 'keywords' => ''],
                ['url' => 'driver_profile', 'title' => 'Driver Profile', 'description' => 'Driver Profile', 'keywords' => ''],
                ['url' => 'documents/{id}', 'title' => 'Driver Documents', 'description' => 'Driver Documents', 'keywords' => ''],
                ['url' => 'driver_payment', 'title' => 'Driver Payment', 'description' => 'Driver Payment', 'keywords' => ''],
                ['url' => 'driver_invoice', 'title' => 'Driver Invoice', 'description' => 'Driver Invoice', 'keywords' => ''],
                ['url' => 'driver_trip', 'title' => 'Driver Trips', 'description' => 'Driver Trips', 'keywords' => ''],
                ['url' => 'driver_trip_detail/{id}', 'title' => 'Driver Trips Details', 'description' => 'Driver Trips Details', 'keywords' => ''],
                ['url' => 'download_invoice/{id}', 'title' => 'Invoice', 'description' => 'Invoice', 'keywords' => ''],
                ['url' => 'trip', 'title' => 'Trips', 'description' => 'Trips', 'keywords' => ''],
                ['url' => 'profile', 'title' => 'Profile', 'description' => 'Profile', 'keywords' => ''],
                ['url' => 'forgot_password_driver', 'title' => 'Forgot Password', 'description' => 'Forgot Password', 'keywords' => ''],
                ['url' => 'reset_password', 'title' => 'Reset Password', 'description' => 'Reset Password', 'keywords' => ''],
                ['url' => 'forgot_password_rider', 'title' => 'Forgot Password', 'description' => 'Forgot Password', 'keywords' => ''],
                ['url' => 'forgot_password_link/{id}', 'title' => 'Forgot Password Link', 'description' => 'Forgot Password Link', 'keywords' => ''],
                ['url' => 'payout_preferences', 'title' => 'Payout Preferences', 'description' => 'Payout Preferences', 'keywords' => ''],
                ['url' => 'help', 'title' => 'Help Center', 'description' => 'Help Center', 'keywords' => ''],
                ['url' => 'help/topic/{id}/{category}', 'title' => 'Help Center', 'description' => 'Help Center', 'keywords' => ''],
                ['url' => 'help/article/{id}/{question}', 'title' => 'Help Center', 'description' => 'Help Center', 'keywords' => ''],
                ['url' => 'signin_company', 'title' => 'Sign In Company', 'description' => 'Sign In Company', 'keywords' => ''],
                ['url' => 'signup_company', 'title' => 'Sign Up Company', 'description' => 'Sign Up Company', 'keywords' => ''],
                ['url' => 'forgot_password_company', 'title' => 'Forgot Password', 'description' => 'Forgot Password', 'keywords' => ''],
                ['url' => 'company/reset_password', 'title' => 'Reset Password', 'description' => 'Reset Password', 'keywords' => ''],
                
            ]);
    }
}
