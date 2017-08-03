# api-client
灵析 Api 通用 Client，基于 guzzlehttp/guzzle 实现

[![Build Status](https://travis-ci.org/lingxi/api-client.svg?branch=master)](https://travis-ci.org/lingxi/api-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lingxi/api-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lingxi/api-client/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/lingxi/api-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lingxi/api-client/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/lingxi/api-client/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lingxi/api-client/build-status/master)
### 安装

composer.json 中添加

```shell
composer require lingxi/api-client
```

### Feature
- 灵析 api 接口自动验证
- 支持接口参数自动替换。类似 Laravel 的路由参数替换。请求 uri 中 {user_id} 会被 $query\['user_id'] 的值替换

### 用法

#### 初始化
> 全部参数都可以使用 Setter 方法设置。即不需要再实例化的时候传递参数。
```php
$apiOptions = [
    'base_uri'    => 'http://api.lingxi360.com',
    'api_key'     => 'api_key',
    'api_secret'  => 'api_secret',
];
$apiClient = new ApiClient($apiOptions);
```
##### option 允许的值

|  选项  |  说明  |  默认值  |
| -----  |  -----  |  -----  |
|  `base_uri`      |  `base_uri`    |   Empty String    |
|  `time_out`      |  请求超时时间  |  `5.0`  |
|  `api_key`       |  `api_key`  |    Empty String   |
|  `api_secret`    |  `api_secret`    |   Empty String    |
|  `api_version`   |   Api 版本    |   'v1'    |

##### 链式操作
```php
$apiClient  = new ApiClient();
$apiClient->setBaseUri($apiOptions['base_uri'])
    ->setApiVersion($apiOptions['api_version'])
    ->setConsumer($apiOptions['api_key'], $apiOptions['api_secret'])
    ->setApiType($apiOptions['api_type'])
    ->setOutTime($apiOptions['time_out']);
```
#### 基本操作
```php
// 获取 http 请求对象，返回 GuzzleHttp\Psr7\Request 实例
$apiClient->get($uri, $query)->getRequest();
// 获取请求的数据
$apiClient->get($uri, $query)->getRequestData();
// 获取 Http 响应对象。返回 GuzzleHttp\Psr7\Response 的实例
$apiClient->get($uri, $query)->getResponse();
// post 操作
$apiClient->post($uri, $data)->getResponse();
// put 操作
$apiClient->put($uri, $data)->getResponse();
// delete 操作
$apiClient->delete($uri, $data)->getResponse();
// patch 操作
$apiClient->patch($uri, $data)->getResponse();
// 发起一个请求。$method 为 HTTP 请求动词 
$apiClient->request($method, $uri, $data)->getResponse();
// 获取 api 返回的数据
$apiClient->get($uri, $query)->getResponseData();
// 获取 Http 响应码
$apiClient->get($uri, $query)->getResponseCode();
// 获取最后一次请求的 URL
$apiClient->getLastUrl();
// 获取请求传输时间
$apiClient->getTransferTime();
```

#### Getter
```php
$apiClient->getBaseUri();
$apiClient->getApiVersion();
$apiClient->getOutTime();
$apiClient->getApiKey();
$apiClient->getApiSecret();
```
#### Setter
```php
$apiClient->setBaseUri();
$apiClient->setBaseUri();
$apiClient->setApiVersion();
$apiClient->setOutTime();
```
## License
MIT

