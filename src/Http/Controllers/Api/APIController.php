<?php

namespace ManoCode\AiRoles\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ManoCode\AiRoles\Library\QwenStream;
use ManoCode\AiRoles\Models\AiRole;
use ManoCode\CustomExtend\Traits\ApiResponseTrait;

/**
 *
 */
class APIController extends Controller
{
    use ApiResponseTrait;

    /**
     * 获取角色列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoleLists(): \Illuminate\Http\JsonResponse
    {
        $baseQuery = AiRole::query()->where('state', '>', 0);
        $baseQuery->select([
            'id',
            'cate_id',
            'role_name',
            'app_id',
            'role_avatar',
            'desc',
            'created_at'
        ]);
        return $this->success('获取成功', [
            'lists' => $baseQuery->get()
        ]);
    }

    /**
     * 对话
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function chat(Request $request)
    {
        if (strlen($request->input('message', '')) <= 0) {
            return $this->fail('需求不能为空');
        }
        if(intval($request->input('role_id')) <=0){
            return $this->fail('角色不能为空');
        }
        if(!($roleInfo = AiRole::query()->where('id',intval($request->input('role_id')))->first())){
            return $this->fail('角色不存在');
        }
        $message = [
            ['role' => 'system', 'content' => $roleInfo->getAttribute('system_prompt')],
            ['role' => 'user', 'content' => request()->input('message', '')]
        ];
        app()->make(QwenStream::class)->chat($message);
    }

}
