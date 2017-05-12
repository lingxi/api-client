# api-client
灵析 Api 通用 Client，基于 guzzlehttp/guzzle 实现

## 安装

composer.json 中添加

```shell
composer require lingxi/api-client
```

## Feature
- 灵析 api 接口自动验证
- 支持接口参数自动替换。类似 Laravel 的路由参数替换。

## 用法

### 初始化
> 全部参数都可以使用 Setter 方法设置。即不需要再实例化的时候传递参数。
```php
$apiOptions = [
    'base_uri'    => 'http://apixtest.lingxi360.com',
    'api_key'     => '5HQDDYDl1',
    'api_secret'  => 'NsqNx2Plv8eI1xwjr9QuCm6rl0nfThrk',
];
$apiClient = new ApiClient($apiOptions);
```
### option 允许的值

|  选项  |  说明  |  默认值  |
| -----  |  -----  |  -----  |
|  `base_uri`      |  `base_uri`    |   Empty String    |
|  `time_out`      |  请求超时时间  |  `5.0`  |
|  `api_key`       |  `api_key`  |    Empty String   |
|  `api_secret`    |  `api_secret`    |   Empty String    |
|  `api_type`      |  `api_type` (可选 `api_key`,`partnet_key`)     |   'api_key'   |
|  `api_version`   |   Api 版本    |   'v1'    |

### 设置 api 版本号
```php
$apiClient->setApiVersion('v1');
```
### 设置请求超时时间
``php
$apiClient->setApiOutTime(3.0);
``

### 设置请求超时时间
```php
$apiClient->setApiOutTime(3.0);
```
### 设置 api 请求类型
目前只有两种（api_key,partner_key，默认为 api_key）

```php
$apiClient->setApiType('api_key');
```
### 链式操作

```php
$apiClient  = new ApiClient();
$apiClient->setBaseUri($apiOptions['base_uri'])
    ->setApiType($apiOptions['api_version'])
    ->setApiVersion($apiOptions['api_version'])
    ->setConsumer($apiOptions['api_key'], $apiOptions['api_secret'])
    ->setApiType($apiOptions['api_type'])
    ->setOutTime($apiOptions['time_out']);
```
### 获取 Http 响应码
```php
$apiClient->get($uri, $param)->getResponseCode();
```
### 获取最后一次请求的 URL
```php
$apiClient->getLastUrl();
```
### 获取响应数据
```php
$apiClient->get($uri, $param)->getResponseData();
$apiClient->post($uri, $param)->getResponseData();
$apiClient->put($uri, $param)->getResponseData();
$apiClient->request($method, $uri, $param)->getResponseData();
```
###  获取 meta
```php
$apiClient->get($uri, $param)->getMeta();
```
### 获取数据列表
```php
$apiClient->get($uri, $param)->getData();
```
### 获取处理后的单个数据
```php
$apiClient->get($uri, $param)->getItem();
```
### get 数据缓存
注：`cache` 方法只能用于 get 请求
```php
// $cacheTime 为缓存时间（默认：1 ，单位：分钟）
// $cacheTime=0 表示不缓存
$apiClient->get($uri, $param,$cacheTime)->getItem();
// 没有第二个参数
$apiClient->get($uri, $cacheTime)->getItem();
```
### Getter
```php
$apiClient->getBaseUri();
$apiClient->getApiType();
$apiClient->getApiVersion();
$apiClient->getApiType();
$apiClient->getOutTime();
$apiClient->getApiKey();
$apiClient->getApiSecret();
```
### Setter
```php
$apiClient->setBaseUri();
$apiClient->setApiType();
$apiClient->setApiVersion();
$apiClient->setApiType();
$apiClient->setOutTime();
$apiClient->setApiKey();
$apiClient->setApiSecret();
```

