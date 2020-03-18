<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); // user who shares their referral code
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('referral_id')->unsigned(); // user who uses another users code to sign up
            $table->foreign('referral_id')->references('id')->on('users');
            $table->enum('user_type',['Rider', 'Driver']);
            $table->integer('days')->unsigned();
            $table->integer('trips')->unsigned();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('currency_code',10)->nullable();
            $table->foreign('currency_code')->references('code')->on('currency');
            $table->decimal('amount', 11, 2);
            $table->decimal('pending_amount', 11, 2);
            $table->enum('payment_status',['Pending', 'Expired', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_users');
    }
}
