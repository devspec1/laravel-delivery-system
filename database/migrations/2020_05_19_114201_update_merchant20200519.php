<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMerchant20200519 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('squareup_id')->after('shared_secret');
            $table->decimal('delivery_fee', 11, 2)->nullable();
            $table->decimal('delivery_fee_base_distance', 11, 2)->nullable();
            $table->decimal('delivery_fee_per_km', 11, 2)->nullable();
            $table->integer('user_id')->after('id');
            $table->string('cuisine_type')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
