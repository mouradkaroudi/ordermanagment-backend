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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id'); // Foreign key to orders table 
            $table->integer('delegate_id');
            $table->integer('quantity'); //
            $table->enum('status', ['under_review', 'completed', 'missing_quantity', 'excessive_amount'])->default('under_review');
            $table->integer('inventory_quantity')->nullable();
            $table->string('return_invoice_id')->nullable();
            $table->boolean('is_from_warehouse')->default(false);
            $table->integer('reviewier_id')->nullable(); // user who reviewed the purchase
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
        Schema::dropIfExists('purchases');
    }
};
