<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('currency');

        Schema::create('currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('code', 10)->unique();
            $table->string('symbol', 10);
            $table->decimal('rate', 10, 2);
            $table->enum('status', ['Active','Inactive'])->default('Active');
            $table->enum('default_currency', ['1','0']);
            $table->enum('paypal_currency', ['Yes','No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currency');
    }
}
