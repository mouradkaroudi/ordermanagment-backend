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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->unique();
            $table->string('name');
            $table->integer('image_id')->nullable();
            $table->string('sku')->unique();
            $table->string('mainRef')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->decimal('cost', 8, 2);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
