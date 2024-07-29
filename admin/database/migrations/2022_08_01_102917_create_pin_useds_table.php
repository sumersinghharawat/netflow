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
        Schema::create('pin_useds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epin_id')->constrained('pin_numbers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('used_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('amount', 14,4)->default(0);
            $table->string('used_for')->nullable()->command('registration, upgrade, etc');
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
        Schema::dropIfExists('pin_useds');
    }
};
