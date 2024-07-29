<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('favicon')->nullable();
            $table->text('address');
            $table->text('fb_link')->nullable();
            $table->text('twitter_link')->nullable();
            $table->text('insta_link')->nullable();
            $table->text('gplus_link')->nullable();
            $table->integer('fb_count')->nullable();
            $table->integer('insta_count')->nullable();
            $table->integer('gplus_count')->nullable();
            $table->string('login_logo')->nullable();
            $table->string('logo_shrink')->nullable();
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
        Schema::dropIfExists('company_profiles');
    }
}
