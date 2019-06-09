<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('活动名称');
            $table->string('front_cover')->comment('封面');
            $table->integer('limit')->comment('人数上限');
            $table->string('address')->comment('地址');
            $table->timestamp('apply_start')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('活动报名时间开始');
            $table->timestamp('apply_end')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('活动报名时间结束');
            $table->timestamp('activity_start')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('活动开始时间');
            $table->timestamp('activity_end')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('活动结束时间');
            $table->string('activity_intro')->comment('活动介绍');
            $table->text('content')->comment('商户介绍');
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
        Schema::dropIfExists('activities');
    }
}
