<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitySignUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_sign_ups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('activity_id')->comment('活动id');
            $table->string('contact_name')->comment('联系人');
            $table->string('contact_phone')->comment('联系手机');
            $table->string('sign_up_reason')->comment('报名理由');
            $table->integer('share_times')->default(0)->comment('分享次数');
            $table->tinyInteger('status')->default(0)->comment('状态：0未成功、1已成功');
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
        Schema::dropIfExists('activity_sign_ups');
    }
}
