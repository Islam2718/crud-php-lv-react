<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->string('payment_type')->nullable(); //debit or credit
            $table->string('payment_method')->nullable(); //bkash, rocket, nagad, cash etc. 
            $table->string('customer_type')->nullable(); // walk-in or Registered
            $table->json('items')->nullable();

            $table->double('bill_total', 15, 9)->nullable();
            $table->double('payment', 15, 9)->nullable();
            $table->double('due', 15, 9)->nullable();
;
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('payments');
    }
};
