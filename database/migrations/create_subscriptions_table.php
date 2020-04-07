<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('subscriptions');
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->text('stripe_id');
            $table->text('status');
            $table->text('email');
            $table->text('card_name');
            $table->text('country');
            $table->text('plan_id');
            $table->text('plan');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subscriptions');
    }
}
