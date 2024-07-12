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
        Schema::create('ai_roles_cate', function (Blueprint $table) {
            $table->comment('AI角色分类');
            $table->increments('id');
            $table->string('cate_name', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->default('')->comment('分类名称');
            $table->string('cate_desc', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->default('')->comment('分类描述');
            $table->string('cate_icon', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->default('')->comment('分类图标');
            $table->tinyInteger('state')->default(0)->comment('状态');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_roles_cate');
    }
};
