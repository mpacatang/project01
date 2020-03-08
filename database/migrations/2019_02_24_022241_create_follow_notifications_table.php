<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id')->unsigned();
            $table->integer('follow_id')->unsigned();
            $table->timestamps();
            $table->foreign('notification_id')
                  ->references('id')->on('notifications')
                  ->onDelete('cascade');
            $table->foreign('follow_id')
                  ->references('id')->on('follows')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow_notifications');
    }
}
