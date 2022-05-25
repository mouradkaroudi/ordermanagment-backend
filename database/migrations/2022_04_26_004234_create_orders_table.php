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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->float('product_cost');
            $table->boolean('is_paid');
            $table->integer('delegate_id')->nullable();
            $table->enum('status', [
                'sent', // Sent order to a delegate 
                'purchased', // Order purchased with full quantity
                'uncompleted_quantity', // Order purchased with partial quantity
                ]
            )->nullable();
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
        Schema::dropIfExists('orders');
    }
};
