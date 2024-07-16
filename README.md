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




#### 接口文档

1. 获取分类以及 分类下的角色列表
```
http://localhost:8000/airole/airole/get-role-lists
```

2. 获取角色的问题提示

```
http://localhost:8000/airole/get-role-questions?role_id=1
```

3. 发起对话

```
http://localhost:8000/airole/chat?role_id=1&message=帮我写一个电脑推销方案
```

