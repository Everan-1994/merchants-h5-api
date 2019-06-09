<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTryUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('try_uses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('产品名称');
            $table->string('front_cover')->comment('封面');
            $table->integer('stock')->comment('库存');
            $table->decimal('price', 10, 2)->comment('价格');
            $table->timestamp('apply_start')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('报名时间开始');
            $table->timestamp('apply_end')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('报名时间结束');
            $table->string('activity_intro')->comment('活动介绍');
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
        Schema::dropIfExists('try_uses');
    }
}
