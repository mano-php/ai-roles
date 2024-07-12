<?php

namespace ManoCode\AiRoles\Services;

use Illuminate\Support\Facades\Http;
use ManoCode\AIHelper\AiHelperServiceProvider;
use ManoCode\AiRoles\Models\AiRole;
use Slowlyo\OwlAdmin\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
/**
 * AI角色管理
 *
 * @method AiRole getModel()
 * @method AiRole|\Illuminate\Database\Query\Builder query()
 */
class AiRoleService extends AdminService
{
	protected string $modelName = AiRole::class;

	public function searchable($query)
	{
		parent::searchable($query);
        $query->when($this->request->input('state'), fn($q) => $q->where('state', $this->request->input('state')));
        $query->when($this->request->input('cate_id'), fn($q) => $q->where('cate_id', $this->request->input('cate_id')));
        $query->when($this->request->input('prompt'),fn($q)=>$q->where('prompt', 'like', '%' . $this->request->input('prompt') . '%'));
        $query->when($this->request->input('model_id'),fn($q)=>$q->where('model_id', $this->request->input('model_id')));
        $query->when($this->request->input('system_prompt'),fn($q)=>$q->where('system_prompt', 'like', '%' . $this->request->input('system_prompt') . '%'));

	}
    public function requested($prompt)
    {
        // 设置最大执行时间为300秒
        set_time_limit(300);

        $app_url = AiHelperServiceProvider::setting('app_url');
        $app_key = AiHelperServiceProvider::setting('app_key');
        admin_abort_if(empty($app_key), '秘钥不存在，请先在插件配置中设置！');
        admin_abort_if(empty($app_url), '请求地址不存在，请先在插件配置中设置！');

        $message = [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => ''],
                ['role' => 'user', 'content' => $prompt],
            ],
            'stream' => true
        ];

        return response()->stream(function () use ($app_url, $app_key, $message) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $app_key
            ])->withOptions([
                'stream' => true
            ])->post($app_url . "/v1/chat/completions", $message);

            foreach ($response->getBody() as $chunk) {
                $messages = $this->parseEventStreamData($chunk);
                foreach ($messages as $message) {
                    foreach ($message['choices'] ?? [] as $choice) {
                        $str = $choice['delta']['content'] ?? '';
                        echo $str;
                        ob_flush();
                        flush();
                    }
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    private function parseEventStreamData($response): array
    {
        $data = [];
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }
            [$name, $value] = explode(':', $line, 2);
            if ($name == 'data') {
                $data[] = json_decode(trim($value), true);
            }
        }
        return $data;
    }

}
