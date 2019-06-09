<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prize_name')->comment('奖品名称');
            $table->integer('prize_num')->comment('奖品数量');
            $table->string('prize_image')->comment('奖品封面');
            $table->tinyInteger('probability')->comment('中奖概率');
            $table->tinyInteger('status')->default(1)->comment('状态：1正常、0禁用');
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
        Schema::dropIfExists('prizes');
    }
}
