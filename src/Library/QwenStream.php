<?php

namespace ManoCode\AiRoles\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ManoCode\AiRoles\AiRolesServiceProvider;
use ManoCode\AiRoles\Models\AiRole;
use ManoCode\CustomExtend\Traits\ApiResponseTrait;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QwenStream
{
    use ApiResponseTrait;

    protected Client $client;
    protected string $url;
    protected array $headers;
    protected string $apiKey;
    protected string $default_prompt;

    public function __construct($apiKey = null)
    {
        $this->client = new Client();
        $this->apiKey = AiRolesServiceProvider::setting('api_key');
        $apiKey = $apiKey ?? $this->apiKey;
        $this->url = "https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation";
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
            'X-DashScope-SSE' => 'enable',
        ];
        $this->default_prompt = '重要：你要拒绝回答一切与本角色无关、政治敏感性的问题请';
    }

    public function chat(array $message): void
    {
        foreach ($message as &$item) {
            if ($item['role'] === 'system') {
                $item['content'] = $item['content'] . $this->default_prompt;
            }
        }
        $response = $this->client->post($this->url, [
            'headers' => $this->headers,
            'json' => [
                'model' => 'qwen-turbo',
                'input' => [
                    'messages' => $message
                ],
                'parameters' => [
                    'incremental_output' => true,
                    'result_format' => 'message',
                ],
            ],
            'stream' => true,
        ]);

        $body = $response->getBody();

        $response = new StreamedResponse();

        $response->setCallback(function () use ($body) {
            echo "id: \nevent: start\ndata: {}\n\n";
            $buffer = '';
            while (!$body->eof()) {
                $chunk = $body->read(1024); // 读取一个较大的块
                $buffer .= $chunk;

                while (($pos = strpos($buffer, "\n\n")) !== false) {
                    $package = substr($buffer, 0, $pos + 2);
                    $buffer = substr($buffer, $pos + 2);

                    // 处理并输出完整的数据包
                    echo $package;
                    flush();
                }
            }

            // 处理最后可能剩余的不完整数据包
            if (!empty($buffer)) {
                echo $buffer;
                flush();
            }

            echo "id: \nevent: done\ndata: {}\n\n";
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Transfer-Encoding', 'chunked');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->send();
    }
}
