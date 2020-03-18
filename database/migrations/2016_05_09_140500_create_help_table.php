<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('help_category');
            $table->integer('subcategory_id')->nullable();
            $table->string('question');
            $table->enum('suggested', ['yes', 'no'])->default('no');
            $table->enum('status', ['Active','Inactive'])->default('Active');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE help ADD answer MEDIUMBLOB AFTER question");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('help');
    }
}
