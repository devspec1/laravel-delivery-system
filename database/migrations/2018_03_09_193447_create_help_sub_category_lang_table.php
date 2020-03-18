<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpSubCategoryLangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_sub_category_lang', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sub_category_id')->unsigned();
            $table->string('name');   
            $table->longText('description');   
            $table->string('locale',5)->index();
            $table->unique(['sub_category_id','locale']);
            $table->foreign('sub_category_id')->references('id')->on('help_subcategory')->onDelete('cascade');
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
        Schema::drop('help_sub_category_lang');
    }
}
