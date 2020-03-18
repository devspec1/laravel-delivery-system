<?php

use Illuminate\Database\Seeder;

class ApiCredentialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('api_credentials')->delete();
        
        DB::table('api_credentials')->insert([
            ['name' => 'key', 'value' => 'AIzaSyAi1MxUOrw3ze_OAFNATrZjKh--JFsTruk', 'site' => 'GoogleMap'],
            ['name' => 'server_key', 'value' => 'AIzaSyAi1MxUOrw3ze_OAFNATrZjKh--JFsTruk', 'site' => 'GoogleMap'],
            ['name' => 'sid', 'value' => 'ACf64f4d6b2a55e7c56b592b6dec3919ae', 'site' => 'Twillo'],
            ['name' => 'token', 'value' => 'bc887b0e7159ab5cb0945c3fc59b345a', 'site' => 'Twillo'],
            ['name' => 'from', 'value' => '+15594238858', 'site' => 'Twillo'],
            ['name' => 'server_key', 'value' => 'AIzaSyB0efJyL4VKIbR2rTcugSC_z-m3z06hjEk', 'site' => 'FCM'],
            ['name' => 'sender_id', 'value' => '253756802947', 'site' => 'FCM'],                
            ['name' => 'client_id', 'value' => '1105678852897547', 'site' => 'Facebook'],
            ['name' => 'client_secret', 'value' => '64c4d6d3dc2ba3471297c17585a60aff', 'site' => 'Facebook'],
            ['name' => 'client_id', 'value' => '200332964350-lkr7e12upf315qpg404a402s31f4qncn.apps.googleusercontent.com', 'site' => 'Google'],
            ['name' => 'client_secret', 'value' => 'SPe8bYCFXpv8oDyygaWrofJw', 'site' => 'Google'],
            ['name' => 'sinch_key', 'value' => 'c9ea329a-d57f-4cb3-b640-a183799ba839', 'site' => 'Sinch'],
            ['name' => 'sinch_secret_key', 'value' => 'muqN5Q/zuEeZV9ZqrTTmHg==', 'site' => 'Sinch'],
        ]);
    }
}
