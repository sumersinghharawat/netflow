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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->tinyInteger('react')->default(0);
            $table->tinyInteger('admin_only')->default(1);
            $table->tinyInteger('react_only')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->bigInteger('order')->nullable();
            $table->bigInteger('child_order')->nullable();
            $table->boolean('is_heading')->default(0);
            $table->boolean('has_children')->default(0);
            $table->boolean('is_child')->default(0);
            $table->boolean('side_menu')->default(1);
            $table->boolean('settings_menu')->default(0);
            $table->longText('user_icon')->nullable();
            $table->longText('admin_icon')->nullable();
            $table->longText('mobile_icon')->nullable();
            $table->longText('route_name')->nullable();
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
        Schema::dropIfExists('menus');
    }
};
