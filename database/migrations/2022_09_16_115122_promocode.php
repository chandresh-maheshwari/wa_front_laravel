<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Promocode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // creating table 
       Schema::create('promocode', function (Blueprint $table) {
        $table->id();
        $table->string('code');
        $table->string('name');
        $table->date('period_by');
        $table->date('period_to');
        $table->string('product_dropdown');
        $table->int('is_deleted');
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
        //
    }
}
