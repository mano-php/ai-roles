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
    public function pushChat()
    {

            $message = [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => '写个歌']
            ];
            $body = [
                'model' => 'qwen-turbo',
                'input' => [
                    'messages'=>$message
                ],
                'parameters'=>[
                    'incremental_output' => true,
                    'result_format' => 'message',
                ],
            ];
            try {
            $response = $this->client->post($this->url, [
                'headers' =>  $this->headers,
                'json' => $body,
                'stream' => true,
            ]);

            $body = $response->getBody();
            if (ob_get_level() == 0) ob_start();
//            if (ob_get_level()) ob_end_clean();
//            ob_implicit_flush(1);
            while (!$body->eof()) {
                $line = $body->read(1024);
                $this->processStreamData($line);
            }
            ob_end_flush();

        } catch (RequestException $e) {
            Log::error('Request failed: ' . $e->getMessage());
            return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
        }
    }
    public function qwenChat()
    {

        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache');
//        header('Transfer-Encoding: chunked');
        header('X-Accel-Buffering: no');
        $this->pushChat();
//        return  new StreamedResponse(function() {
//            $this->pushChat();
//            echo PHP_EOL;
//        }, 200, [
//            'Content-Type' => 'text/event-stream',
//            'Cache-Control' => 'no-cache',
//            'X-Accel-Buffering' => 'no',
//        ]);
    }
    protected function processStreamData($response)
    {
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            if (strpos($line, 'data:') === 0) {
                $data = substr($line, 5);
                $decodedData = json_decode($data, true);
                if (isset($decodedData['output']['choices'][0]['message']['content'])) {
                    echo $decodedData['output']['choices'][0]['message']['content'];
                    ob_flush();
                    flush();
                }
            }
        }
    }
}
