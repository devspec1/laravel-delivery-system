<?php

use Illuminate\Database\Seeder;

class TollReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('toll_reasons')->delete();

        DB::table('toll_reasons')->insert([
            ['id' => '1','reason' => 'Other Fees','status'=>'Active'],
        ]);
    }
}
