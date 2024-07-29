<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_boxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user_id');
            $table->foreign('from_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->foreign('to_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->tinyInteger('to_all')->default(0);
            $table->text('subject');
            $table->longText('message');
            $table->date('date');
            $table->tinyInteger('read_status')->default(0)->comment('0 for false 1 for true');
            $table->tinyInteger('inbox_delete_status')->default(0);
            $table->tinyInteger('sent_delete_status')->default(0);
            $table->string('deleted_by')->nullable();
            $table->string('parent_user_mail_id')->nullable();
            $table->foreignId('thread')->nullable()->constrained('mail_boxes')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('mail_boxes');
    }
}
