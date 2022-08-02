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
        Schema::create('product_listing_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('issue')->default('unavailable_quantity');
            $table->integer('created_by');
            $table->timestamp('created_at');
            $table->integer('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_listings');
    }
};
