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
        // 角色以及分类
        \ManoCode\AiRoles\Models\AiRole::query()->insert(collect(json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'roles.json'), true))->map(function ($item) {
            if(!($cate = \ManoCode\AiRoles\Models\AiRolesCate::query()->where('name',$item['cate']['name'])->first())){
                $cate = new \ManoCode\AiRoles\Models\AiRolesCate();
                $cate->setAttribute('cate_name',$item['cate']['name']);
                $cate->setAttribute('cate_icon',$item['cate']['icon']);
                $cate->setAttribute('created_at',date('Y-m-d H:i:s'));
                $cate->setAttribute('updated_at',date('Y-m-d H:i:s'));
                $cate->save();
            }
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
            $insertData['cate_id'] = $cate->getAttribute('id');
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
