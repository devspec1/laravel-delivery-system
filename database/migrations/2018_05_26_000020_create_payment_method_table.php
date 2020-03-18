<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentMethodTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $schema_table = 'payment_method';

    /**
     * Run the migrations.
     * @table user_payment_method
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('customer_id', 100)->nullable();
            $table->string('intent_id', 100)->nullable();
            $table->string('payment_method_id', 100)->nullable();
            $table->string('brand',20)->nullable();
            $table->integer('last4')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->schema_table);
    }
}