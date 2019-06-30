<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winnings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('prize_name')->comment('奖品名称');
            $table->string('prize_img')->comment('奖品图片');
            $table->tinyInteger('prize_status')->default(0)->comment('是否是奖品 0否 1是');
            $table->string('contact_name')->comment('联系人');
            $table->string('contact_phone')->comment('联系手机');
            $table->string('province')->comment('省');
            $table->string('city')->comment('市');
            $table->string('district')->comment('区');
            $table->string('address')->comment('详细地址');
            $table->tinyInteger('status')->default(0)->comment('状态：0未处理、1处理中、2已完成');
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
        Schema::dropIfExists('winnings');
    }
}
