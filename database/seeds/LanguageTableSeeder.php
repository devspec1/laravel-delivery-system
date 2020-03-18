<?php

use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('language')->delete();
    	
        DB::table('language')->insert([
            ['name' => 'English','value' => 'en','default_language' => '1','status' => 'Active'],
            ['name' => 'Persian','value' => 'fa','default_language' => '0','status' => 'Active'],
            ['name' => 'Arabic','value' => 'ar','default_language' => '0','status' => 'Active'],
            ['name' => 'Spanish','value' => 'es','default_language' => '0','status' => 'Active'],
            ['name' => 'Português','value' => 'pt','default_language' => '0','status' => 'Active'],
        ]);
    }
}
