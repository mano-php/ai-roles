<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_roles', function (Blueprint $table) {
            $table->comment('AI角色管理');
            $table->increments('id');
            $table->integer('cate_id')->default(0)->comment('角色分类ID');
            $table->string('role_name', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->default('')->comment('角色名字');
            $table->bigInteger('user_id')->nullable()->comment('用户ID');
            $table->bigInteger('corp_id')->nullable()->comment('租户ID');
            $table->bigInteger('app_id')->nullable()->comment('应用ID');
            $table->tinyInteger('state')->nullable()->comment('状态');
            $table->string('desc', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->default('')->comment('角色介绍');
            $table->string('role_avatar', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->default('')->comment('角色图标');
            $table->json('prompt')->nullable()->comment('角色提示词');
            $table->json('system_prompt')->nullable()->comment('系统提示词');
            $table->json('questions')->nullable()->comment('问答列表');
            $table->tinyInteger('platform')->default(0)->comment('平台');
            $table->tinyInteger('model_id')->nullable()->comment('调用模型ID');
            $table->tinyInteger('output_type')->default(0)->comment('输出类型');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_roles');
    }
};
