<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCustomConfigGroupsTable extends Migration
{

    public function up()
    {
        if (!Schema::hasTable('admin_custom_config_groups')) {
            Schema::create('admin_custom_config_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('分组名称');
                $table->string('slug')->unique()->comment('分组唯一标识');
                $table->unsignedInteger('order')->default(100)->comment('排序');
                $table->timestamps();
                $table->softDeletes();

                // 设置索引
                $table->index('slug');
                $table->index('order');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('admin_custom_config_groups');
    }
}
