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
        Schema::create('stripe_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('packages')->nullable();
            $table->string('stripe_product_id',200)->nullable();
            $table->string('price_id',200)->nullable();
            $table->longText('product_data')->nullable();
            $table->longText('price_data')->nullable();
            $table->tinyInteger('status')->comment('0:inactive, 1:active')->default(1);
            $table->decimal('amount', 14,4);
            $table->string('type')->nullable();
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
        Schema::dropIfExists('stripe_products');
    }
};