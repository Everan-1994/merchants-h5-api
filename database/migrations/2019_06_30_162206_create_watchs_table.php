<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWatchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watchs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('video_id');
            $table->foreign('video_id')->references('id')->on('block_items')->onDelete('cascade');
            $table->unsignedInteger('user_id')->default(0);
            $table->string('ip', 20)->comment('ip地址');
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
        Schema::dropIfExists('watchs');
    }
}
