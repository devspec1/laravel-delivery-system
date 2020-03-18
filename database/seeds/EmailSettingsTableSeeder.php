<?php

use Illuminate\Database\Seeder;

class EmailSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_settings')->delete();

        DB::table('email_settings')->insert([
        		['name' => 'driver', 		'value' => 'smtp'],
        		['name' => 'host', 			'value' => 'smtp.gmail.com'],
        		['name' => 'port', 			'value' => '25'],
        		['name' => 'from_address',  'value' => 'trioangle1@gmail.com'],
        		['name' => 'from_name', 	'value' => 'Gofer'],
        		['name' => 'encryption', 	'value' => 'tls'],
        		['name' => 'username', 		'value' => 'trioangle1@gmail.com'],
        		['name' => 'password', 		'value' => 'hismljhblilxdusd'],
                ['name' => 'domain', 		'value' => 'sandboxcc51fc42882e46ccbffd90316d4731e7.mailgun.org'],
                ['name' => 'secret', 		'value' => 'key-3160b23116332e595b861f60d77fa720'],
        	]);
    }
}
