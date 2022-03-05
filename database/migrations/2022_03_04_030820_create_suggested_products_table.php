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
        Schema::create('suggested_products', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id')->nullable();
            $table->integer('image_id');
            $table->integer('category_id');
            $table->integer('delivery_method_id');
            $table->integer('user_id');
            $table->float('sell_price')->nullable();
            $table->string('sku')->nullable();
            $table->integer('cost')->nullable();
            $table->boolean('is_new')->nullable();
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
        Schema::dropIfExists('suggested_products');
    }
};
