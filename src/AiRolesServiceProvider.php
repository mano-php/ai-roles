<?php

namespace ManoCode\AiRoles;

use Slowlyo\OwlAdmin\Renderers\SelectControl;
use Slowlyo\OwlAdmin\Renderers\TextControl;
use Slowlyo\OwlAdmin\Extend\ServiceProvider;
use ManoCode\CustomExtend\Extend\ManoCodeServiceProvider;

class AiRolesServiceProvider extends ManoCodeServiceProvider
{


    protected $menu = [
        [
            'parent' => 0,
            'title' => 'AI角色管理',
            'url' => '/ai_roles',
            'url_type' => '1',
            'keep_alive' => '1',
            'icon' => 'clarity:employee-line',
        ],
        [
            'parent' => 'AI角色管理', // 此处父级菜单根据 title 查找
            'title' => 'AI角色列表',
            'url' => '/ai_roles/list',
            'url_type' => '1',
            'icon' => 'clarity:employee-line',
        ],
        [
            'parent' => 'AI角色管理', // 此处父级菜单根据 title 查找
            'title' => 'AI角色分类',
            'url' => '/ai_roles/cate',
            'url_type' => '1',
            'icon' => 'clarity:employee-line',
        ],
    ];
    protected $dict = [
        [
            'key'=>'ai.roles.state',
            'value'=>'AI角色状态',
            'keys'=>[
                ['key'=>'1', 'value'=>'启用'],
                ['key'=>'0', 'value'=>'禁用'],
            ]
        ],
    ];
	public function settingForm()
	{
	    return $this->baseSettingForm()->body([
            TextControl::make()->name('api_key')->label('通义千问api_key')->required(true),
            SelectControl::make()->name('model')->label('模型')->options([
                ['label'=>'qwen-turbo', 'value'=>'qwen-turbo'],
                ['label'=>'qwen-turbo-0624', 'value'=>'qwen-turbo-0624'],
                ['label'=>'qwen-plus', 'value'=>'qwen-plus'],
                ['label'=>'qwen-plus-0624', 'value'=>'qwen-plus-0624'],
                ['label'=>'qwen-max', 'value'=>'qwen-max'],
                ['label'=>'qwen-max-0428', 'value'=>'qwen-max-0428'],
                ['label'=>'qwen2-72b-instruct', 'value'=>'qwen2-72b-instruct'],
                ['label'=>'qwen2-7b-instruct', 'value'=>'qwen2-7b-instruct'],
                ['label'=>'qwen2-1.5b-instruct', 'value'=>'qwen2-1.5b-instruct'],
            ])->help('https://help.aliyun.com/zh/model-studio/developer-reference/what-is-tongyi-qianwen-llm?spm=a2c4g.11186623.0.0.52b64dacPk4T6R')
                ->required(true)
	    ]);
	}
}
