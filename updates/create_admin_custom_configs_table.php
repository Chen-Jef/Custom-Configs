<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCustomConfigsTable extends Migration
{

    public function up()
    {
        if (!Schema::hasTable('admin_custom_configs')) {
            Schema::create('admin_custom_configs', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('group_slug',30)->comment('配置分组唯一键');
                $table->string('name',50)->comment('配置名称');
                $table->string('key',30)->unique()->comment('唯一键');
                $table->string('tip',50)->nullable()->comment('提示');
                $table->string('type',15)->comment('类型');
                $table->string('disk',15)->comment('文件存储驱动');
                $table->longText('content')->comment('内容/值');
                $table->unsignedInteger('order')->default(100)->comment('排序');
                $table->timestamps();
                $table->softDeletes();

                // 设置联合索引
                $table->index(['group_slug', 'key']);
                $table->index(['group_slug', 'order']);
                $table->index('key');

                // 设置外键
                $table->foreign('group_slug')
                    ->references('slug')
                    ->on('admin_custom_config_groups')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('admin_custom_configs');
    }
}
