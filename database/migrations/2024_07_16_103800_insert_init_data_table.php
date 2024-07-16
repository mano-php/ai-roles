<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 角色分类
        \ManoCode\AiRoles\Models\AiRolesCate::query()->insert(collect(json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'roles_cate.json'), true))->map(function ($item) {
            $insertData = [];
            $insertData['cate_name'] = $item['tag_name'];
            $insertData['cate_desc'] = $item['tag_name'];
            $insertData['state'] = 1;
            $insertData['created_at'] = date('Y-m-d H:i:s');
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            return $insertData;
        })->toArray());
        // 角色分类
        \ManoCode\AiRoles\Models\AiRole::query()->insert(collect(json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'roles.json'), true))->map(function ($item) {
            $insertData = [];
            $insertData['role_name'] = $item['name'];
            $insertData['desc'] = $item['desc'];
            $insertData['role_avatar'] = $item['avatar'];
            if(isset($item['system'][0]['value'])){
                $insertData['system_prompt'] = json_encode($item['system'][0]['value']);
            }else{
                $insertData['system_prompt'] = json_encode("");
            }
            $insertData['state'] = 1;
            $insertData['questions'] = json_encode($item['questions']);
            $insertData['cate_id'] = \ManoCode\AiRoles\Models\AiRolesCate::query()->where('cate_name', $item['cate']['name'])->value('id');
            $insertData['created_at'] = date('Y-m-d H:i:s');
            $insertData['updated_at'] = date('Y-m-d H:i:s');
            return $insertData;
        })->toArray());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
