# AI角色拓展
可以管理AI角色的拓展，包括：
  - 角色定位
  - 角色功能
  - 角色能力
  - 输出规范
# 模型管理
通过拓展配置项，配置通义模型的api_key和model
## 调用方式
```PHP
use ManoCode\Library\QwenChat;
$options = [
    'model' => 'qwen-max',//模型名称
    'params' => ['key' => 'value'],//自定义参数
    'isJson' => true //是否返回json格式数据
];

$qwenChat = new QwenChat();
$response = $qwenChat->sendMessage('Hello, World!', 'This is a system prompt.', $options);

```

