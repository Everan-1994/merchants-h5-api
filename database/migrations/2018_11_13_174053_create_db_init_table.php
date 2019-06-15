<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbInitTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // admin_users
        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 190);
            $table->string('realname');
            $table->string('email')->nullable();
            $table->string('password');
            $table->tinyInteger('isEnable')->default(1);
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });

        // admin_roles
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->tinyInteger('isSuper')->default(0);
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });

        // admin_role_users
        Schema::create('admin_role_users', function (Blueprint $table) {
            $table->unsignedInteger('adminRoleId')->index();
            $table->unsignedInteger('adminUserId')->index();
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });

        // actions
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parentId');
            $table->string('name', 32);
            $table->string('route', 100)->nullable();
            $table->string('description')->nullable();
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });

        // admin_role_actions
        Schema::create('admin_role_actions', function (Blueprint $table) {
            $table->unsignedInteger('adminRoleId')->index();
            $table->unsignedInteger('actionId')->index();
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });

        // operation-log
        Schema::create('operation_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 64);
            $table->string('agent');
            $table->string('uri');
            $table->string('route');
            $table->string('params')->nullable();
            $table->text('data')->nullable();
            $table->string('method', 10);
            $table->string('ip', 20);
            $table->string('ipInfo');
            $table->string('code', 6);
            $table->string('message');
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        // blocks
        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('front_cover')->comment('封面');
            $table->integer('watch_times')->default(0)->comment('观看次数');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：0冻结、1正常');
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });

        // blockItems
        Schema::create('block_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blockId')->comment('区块id');
            $table->string('title')->comment('名称');
            $table->string('front_cover')->nullable()->comment('封面');
            $table->string('video')->comment('视频地址');
            $table->integer('watch_times')->default(0)->comment('观看次数');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：0冻结、1正常');
            $table->timestamp('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('admin_roles');
        Schema::dropIfExists('admin_role_users');
        Schema::dropIfExists('actions');
        Schema::dropIfExists('admin_role_actions');
        Schema::dropIfExists('operation_log');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('block_items');
    }
}
