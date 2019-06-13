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
            $table->string('product_intro')->comment('产品介绍');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：0关闭、1正常');
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
