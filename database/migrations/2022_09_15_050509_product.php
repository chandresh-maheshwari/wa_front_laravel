<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Product extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // creating table 
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file');
            $table->int('storage');
            $table->double('price_per_month_6below');
            $table->double('price_per_month_6above');
            $table->double('free_insurance');
            $table->string('charge');
            $table->string('object');
            $table->string('time');
            $table->text('description');
            $table->tinyint('is_deleted');
            $table->timestamps();


            $table->string('title_cn');
            $table->string('file_cn');
            $table->int('storage_cn');
            $table->double('price_per_month_6below_cn');
            $table->double('price_per_month_6above_cn');
            $table->double('free_insurance_cn');
            $table->string('charge_cn');
            $table->string('object_cn');
            $table->string('time_cn');
            $table->text('description_cn');
            // $table->tinyint('is_deleted');
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
