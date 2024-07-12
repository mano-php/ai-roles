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
use Symfony\Component\HttpFoundation\StreamedResponse;

class QwenStream
{
    protected $client;
    protected $url;
    protected $headers;
    protected $apiKey;

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
    }

    public function qwenChat()
    {
        $message = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => '弟子规、每句换行。给我1500字。以外的不需要。']
        ];
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

        $response->setCallback(function () use ($body){
            echo "id: \nevent: start\ndata: {}\n\n";
            while (!$body->eof()) {
                $line = $body->read(1024);
                echo "$line";
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
