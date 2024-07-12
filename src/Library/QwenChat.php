<?php

namespace ManoCode\AiRoles\Library;

use Darabonba\GatewaySpi\Models\InterceptorContext\response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ManoCode\AiRoles\AiRolesServiceProvider;

class QwenChat
{
    protected $client;
    protected $url;
    protected $headers;

    public function __construct($apiKey = null)
    {
        $this->client = new Client();
        $defaultApiKey = AiRolesServiceProvider::setting('api_key');
        $apiKey = $apiKey ?? $defaultApiKey;
        $this->url = "https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation";
        $this->headers = [
            'Authorization' => 'Bearer ' . $apiKey,
        ];
    }

    /**
     * 通用方法调用通义千问，支持POST和流式传输
     *
     * @param string $message 用户消息
     * @param string $prompt 系统提示
     * @param array $options 可选参数，包括模型名称、通配符数据和结果格式等
     * @return array 返回处理结果，包括状态码和消息
     */
    public function sendMessage($message, $prompt='', $options = [])
    {
        $model = $options['model'] ?? AiRolesServiceProvider::setting('model');
        $params = $options['params'] ?? [];
        $isJson = $options['isJson'] ?? false;

        $questions = $params['questions'] ?? [];
        if (!empty($questions)) {
            $formattedQuestions = array_map(function($index, $question) {
                return ($index + 1) . ". " . $question;
            }, array_keys($questions), $questions);
            $questionString = implode("\n", $formattedQuestions);
            $prompt = $prompt . "\n示例问题为：\n" . $questionString;
        }
        // 处理通配符数据
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                if ($key === 'questions') {
                    // questions已经在上面处理过，这里可以跳过
                    continue;
                }
                $stringValue = json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $stringValue = (string)$value;
            }
            $prompt = str_replace('${' . $key . '}', $stringValue, $prompt);
        }
        $messages = [
            ['role' => 'system', 'content' => $prompt],
            ['role' => 'user', 'content' => $message]
        ];
        $data = [
            'model' => $model,
            'input' => [
                'messages' => $messages,
            ],
            'parameters' => [
                'result_format' => $isJson ? 'json' : 'text'
            ],
            'top_p' => 0.6
        ];

        return $this->postRequest($data);
    }

    /**
     * 发送POST请求
     *
     * @param array $data 请求数据
     * @return array 返回处理结果，包括状态码和消息
     */
    private function postRequest($data)
    {
        try {
            $response = $this->client->post($this->url, [
                'headers' => $this->headers,
                'json' => $data
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            return $this->formatResponse(200, '成功', $responseBody['output']);
        } catch (GuzzleException $e) {
            return $this->formatResponse($e->getCode(), $e->getMessage(), null);
        } catch (\Exception $e) {
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    /**
     * 格式化响应
     *
     * @param int $statusCode 状态码
     * @param string $message 消息
     * @param mixed $data 返回数据
     * @return array 格式化的响应
     */
    private function formatResponse($statusCode, $message, $data)
    {
        return [
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];
    }
    /**
     * 发送流式请求
     *
     * @param array $data 请求数据
     * @return \GuzzleHttp\Psr7\Stream 返回处理结果，包括状态码和消息
     */
    public function streamRequest($data)
    {
        // 设置最大执行时间为300秒
        set_time_limit(300);
        $url = $this->url;
        $headers = $this->headers;

        return response()->stream(function () use ($data, $url, $headers) {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $data,
                'stream' => true
            ]);
            dd($response);
            foreach ($response->getBody() as $chunk) {
                $messages = $this->parseEventStreamData($chunk);
                foreach ($messages as $message) {
                    if ($response->getStatusCode() === HTTPStatus::OK) {
                        foreach ($message['output'] ?? [] as $choice) {
                            $str = $choice['delta']['content'] ?? '';
                            echo $str;
                            ob_flush();
                            flush();
                        }
                    } else {
                        echo sprintf(
                            'Request id: %s, Status code: %s, error code: %s, error message: %s',
                            $response->getHeader('X-Request-ID')[0] ?? 'N/A',
                            $response->getStatusCode(),
                            $message['code'] ?? 'N/A',
                            $message['message'] ?? 'N/A'
                        );
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
//        return new \GuzzleHttp\Psr7\Stream($response->getBody());
        try {
            $response = $this->client->post($this->url, [
                'headers' => $this->headers,
                'json' => $data,
                'stream' => true
            ]);

            $body = $response->getBody();
            $responseData = '';
            while (!$body->eof()) {
                $responseData .= $body->read(1024);
            }
            $responseBody = json_decode($responseData, true);
            return $this->formatResponse(200, '成功', $responseBody['output']);
        } catch (GuzzleException $e) {
            return $this->formatResponse($e->getCode(), $e->getMessage(), null);
        } catch (\Exception $e) {
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }
}
