<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payment', function (Blueprint $table) {
            $table->id();  
           
            $table->string('university_student');
            $table->string('instructions');
            $table->string('walk_up');
            $table->string('health_protection');
            $table->string('payment_method');
            $table->string('card_number');
            $table->date('expiree_date');
            $table->string('csv');
            $table->string('status');
            $table->integer('is_deleted');
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
