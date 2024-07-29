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
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->string('tree_icon')->nullable();
            $table->decimal('commission', 12, 2)->default(0);
            $table->unsignedBigInteger('oc_product_id')->nullable()->index('oc_product_id');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('status')->default(1)->comment('0 : Inactive, 1: Active');
            $table->tinyInteger('rank_order')->nullable()->unique();
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
        Schema::dropIfExists('ranks');
    }
};
