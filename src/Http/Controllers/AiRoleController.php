<?php

namespace ManoCode\AiRoles\Http\Controllers;

use AlibabaCloud\Tea\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ManoCode\AIHelper\Services\AIHelperService;
use ManoCode\AiRoles\AiRolesServiceProvider;
use ManoCode\AiRoles\Library\QwenChat;
use ManoCode\AiRoles\Models\AiRole;
use ManoCode\AiRoles\Models\AiRolesCate;
use ManoCode\AiRoles\Services\AiRoleService;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use Slowlyo\OwlAdmin\Renderers\Drawer;
use Slowlyo\OwlAdmin\Renderers\DrawerAction;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Renderers\LinkAction;
use Slowlyo\OwlAdmin\Renderers\Log;
use Slowlyo\OwlAdmin\Renderers\Option;
use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\RadiosControl;
use Slowlyo\OwlAdmin\Renderers\SelectControl;
use Slowlyo\OwlAdmin\Renderers\SubFormControl;
use Slowlyo\OwlAdmin\Renderers\TableColumn;
use Slowlyo\OwlAdmin\Renderers\TextareaControl;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * AI角色管理
 *
 * @property AiRoleService $service
 */
class AiRoleController extends AdminController
{
	protected string $serviceName = AiRoleService::class;

    public function list()
    {
        $crud = $this->baseCRUD()
            ->id('ai_roles')
            ->name('ai_roles')
            ->filterTogglable(false)
            ->headerToolbar([
                $this->createButton(false, 'lg'),
                ...$this->baseHeaderToolBar(),
            ])
            ->columns([
                TableColumn::make()->name('id')->label('角色ID')->sortable(true),
                TableColumn::make()->name('role_name')->label('角色名字'),
                TableColumn::make()->name('cate.cate_name')->label('角色分类'),
                TableColumn::make()->name('role_avatar')->label('角色头像')->type('avatar')->src('${role_avatar}'),
                TableColumn::make()->name('state')->label('状态')->type('status'),
                TableColumn::make()->name('desc')->label('角色介绍'),
//                TableColumn::make()->name('model_id')->label('调用模型ID'),
                TableColumn::make()->name('platform')->label('平台'),
                TableColumn::make()->name('created_at')->label('创建时间')->type('datetime')->sortable(true),
                TableColumn::make()->name('updated_at')->label('更新时间')->type('datetime')->sortable(true),
                $this->rowEditDrawer(true, 'lg'),
                DrawerAction::make()->drawer(
                    Drawer::make()->title('调试')->body($this->testForm())->closeOnOutside(true)->size('lg'),
                )->label('调试')->icon('fa-regular fa-pen-to-square')->level('link')
            ])->data(['test' => '{}',]);

        return $this->baseList($crud);
    }
    protected function rowEditDrawer(bool $dialog = false, string $dialogSize = '')
    {
        if ($dialog) {
            $form = $this->form(true)->api($this->getUpdatePath())->initApi($this->getEditGetDataPath())->onEvent([
                'submitSucc' => [
                    'actions' => [
                        [
                            'actionType' => 'reload',
                            'componentId' => 'chat_role_list',
                        ],
                    ],
                ],
            ]);

            $button = DrawerAction::make()->drawer(
                Drawer::make()->title(__('admin.edit'))->body($form)->closeOnOutside(true)->size('xl'),
            );
        } else {
            $button = LinkAction::make()->link($this->getEditPath());
        }
        return $button->label(__('admin.edit'))->icon('fa-regular fa-pen-to-square')->level('link');
    }

    /**
     * @return array
     */
    protected function testForm()
    {
        return [
            json_decode('{
  "type": "service",
  "id": "u:b3655514384d",
  "body": [
    {
      "type": "tpl",
      "id": "u:5aacf0cd38e7",
      "tpl": "内容",
      "wrapperComponent": "",
      "inline": false
    },
    {
      "type": "flex",
      "className": "p-1",
      "items": [
        {
          "type": "container",
          "body": [
            {
              "type": "input-text",
              "label": "文本",
              "name": "text",
              "id": "u:0e0ed5078468"
            }
          ],
          "size": "xs",
          "style": {
            "position": "static",
            "display": "block",
            "flex": "1 1 auto",
            "flexGrow": "1",
            "flexBasis": "auto"
          },
          "wrapperBody": "",
          "isFixedHeight": "",
          "isFixedWidth": "",
          "id": "u:d99fbb001c1d"
        },
        {
          "type": "container",
          "body": [
            {
              "type": "button",
              "label": "调试",
              "onEvent": {
                "click": {
                  "actions": [
                    {
                      "outputVar": "responseResult",
                      "actionType": "ajax",
                      "args": {
                        "options": [
                        ],
                        "api": {
                          "url": "/ai_roles/test?role_id=${id}&content=${text}",
                          "method": "get"
                        }
                      }
                    },
                    {
                      "componentId": "u:b3655514384d",
                      "actionType": "setValue",
                      "args": {
                        "value": {
                          "test": "${responseResult}"
                        }
                      }
                    }
                  ]
                }
              },
              "id": "u:edf2f9dd34e4"
            }
          ],
          "size": "xs",
          "style": {
            "position": "static",
            "display": "block",
            "flex": "1 1 auto",
            "flexGrow": "1",
            "flexBasis": "auto"
          },
          "wrapperBody": "",
          "isFixedHeight": "",
          "isFixedWidth": "",
          "id": "u:99823c467172"
        },
        {
          "type": "container",
          "body": [
          ],
          "size": "xs",
          "style": {
            "position": "static",
            "display": "block",
            "flex": "1 1 auto",
            "flexGrow": "1",
            "flexBasis": "auto"
          },
          "wrapperBody": "",
          "isFixedHeight": "",
          "isFixedWidth": "",
          "id": "u:8ad0ee8db0bd"
        }
      ],
      "style": {
        "position": "relative"
      },
      "id": "u:19b470693a48"
    },
    {
      "type": "json",
      "id": "u:325e9be7e383",
      "source": "${test}"
    }
  ]
}',true)
        ];
    }

	public function form($isEdit = false)
	{
		return $this->baseForm()->mode('horizontal')->body([
			amis()->TextControl('role_name', '角色名字')->required(),
            amis()->SelectControl('cate_id', '角色分类')->required()->options(
               AiRolesCate::query()->where('state', 1)->get(['id as value','cate_name as label'])->toArray()
            ),
			amis()->TextareaControl('desc', '角色介绍'),
            ManoImageControl('role_avatar', '角色图标'),
//            SelectControl::make()->name('model_id')->label('所属模型')->options([
//                Option::make()->label('GPT3.5')->value('1'),
//                Option::make()->label('GPT4o')->value('2'),
//                Option::make()->label('QwenMax')->value('3'),
//            ])->selectFirst(),
			amis()->TextareaControl('prompt', '角色提示词'),
//            amis()->SubFormControl('system_prompt', '系统提示词')->multiple(true)->desc("系统级提示词，拥有更高的权重，强化每一个要求")->form(
//                Form::make()->body([
//                        RadiosControl::make()->name('role')->label('身份')->options([
//                            Option::make()->label('系统身份')->value("system"),
//                            Option::make()->label('用户身份')->value("user"),
//                        ])->desc("调用大模型时的Prompt")->selectFirst(),
//                        TextareaControl::make()->name('value')->label('提示词内容'),
//                ])
//            ),
			amis()->TextareaControl('system_prompt', '系统提示词'),
			amis()->ArrayControl('questions', '问答列表')->items(['type'=>'input-text']),
//			amis()->SelectControl('model_id', '调用模型'),
			amis()->SelectControl('output_type', '输出类型')->options([
                ['label' => '一次加载', 'value' => 1],
                ['label' => '流式加载', 'value' => 0]
            ])->required(),
            amis()->SwitchControl('state', '状态')->value(1)->onText('启用')->offText('禁用')->required(),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('cate_id', '角色分类ID')->static(),
			amis()->TextControl('role_name', '角色名字')->static(),
			amis()->TextControl('user_id', '用户ID')->static(),
			amis()->TextControl('corp_id', '租户ID')->static(),
			amis()->TextControl('app_id', '应用ID')->static(),
			amis()->SwitchContainer('state', '状态'),
			amis()->TextareaControl('desc', '角色介绍'),
			amis()->Avatar('role_avatar', '角色图标'),
			amis()->TextareaControl('prompt', '角色提示词'),
			amis()->TextControl('system_prompt', '系统提示词')->static(),
			amis()->TextControl('questions', '问答列表')->static(),
			amis()->TextControl('platform', '平台')->static(),
			amis()->TextControl('model_id', '调用模型ID')->static(),
			amis()->TextControl('output_type', '输出类型')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}

    public function  aigen(Request $request)
    {

        $role_id = request()->query('role_id', '');

        $message = request()->query('content', '');
        $systemPmt = AiRole::query()->where('id', $role_id)->first();
        $options = [
//            'model' => 'qwen-max',//模型名称
            'params' => ['questions' => $systemPmt->getAttribute('questions')],//自定义参数
            'isJson' => false //是否返回json格式数据
        ];
        $qwenChat = new QwenChat();
        $response = $qwenChat->sendMessage($message,$systemPmt->getAttribute('system_prompt'),$options);
        return response()->json($response);
    }

    public function demo1()
    {
        $data = [
            'model' => 'qwen-turbo',
            'input' => [
                'messages' => [
                    ['role' => 'system', 'content' => ''],
                    ['role' => 'user', 'content' => '写个歌曲']
                ],
            ],
            'top_p' => 0.6
        ];
        $qwenChat = new QwenChat();
        $stream  = $qwenChat->streamRequest($data);
        return response()->stream(function () use ($stream) {
            while (!$stream->eof()) {
                echo $stream->read(1024);
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }
    public function demo(Request $request)
    {
        $data = [
            'model' => 'qwen-turbo',
            'messages' => [
                [
                    ['role' => 'system', 'content' => ''],
                    ['role' => 'user', 'content' => '写个歌曲']
                ]
            ],
            'stream'=>true,
            'incremental_output'=>true
        ];
        $response = new StreamedResponse(function () use($data) {
            $qwenChat = new QwenChat();
            $stream  = $qwenChat->streamRequest($data);
            echo $stream;
            @ob_flush();
            flush();
        });

        // 设置流式传输的头部信息
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Content-Security-Policy','connect-src "self" http://localhost:8000');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With');
        return $response;
    }

}
