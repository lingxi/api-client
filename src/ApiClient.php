<?php

namespace Lingxi\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Lingxi\ApiClient\Exceptions\ApiClientInitException;
use Lingxi\ApiClient\Exceptions\ResponseDataParseException;
use Lingxi\Signature\Authenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ApiClient
 * @package Lingxi\Packages\ApiClient
 */
class ApiClient
{
    /**
     * @var
     */
    protected $httpClient;
    /**
     * @var mixed|string
     */
    protected $baseUri;
    /**
     * @var float|mixed
     */
    protected $outTime;
    /**
     * @var mixed|string
     */
    protected $apiKey;

    /**
     * @var mixed|string
     */
    protected $apiSecret;

    /**
     * @var mixed|string
     */
    protected $apiVersion;
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;
    /**
     * @var array
     */
    protected $requestData;
    /**
     * @var int
     */
    protected $responseCode;
    /**
     * @var array
     */
    protected $responseBody;
    /**
     * @var
     */
    protected $authenticator;
    /**
     * @var string
     */
    protected $lastUrl = '';
    /**
     * @var double
     */
    protected $transferTime = 0;
    /**
     * @var callable
     */
    protected $completeCallBack;
    /**
     * @var bool
     */
    protected $log = false;
    /**
     * @var $handlerStack
     */
    protected $handlerStack;

    /**
     * ApiClient constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->baseUri    = key_exists('base_uri', $options) ? $options['base_uri'] : '';
        $this->outTime    = key_exists('time_out', $options) ? $options['time_out'] : 5.0;
        $this->apiKey     = key_exists('api_key', $options) ? $options['api_key'] : '';
        $this->apiSecret  = key_exists('api_secret', $options) ? $options['api_secret'] : '';
        $this->apiVersion = key_exists('api_version', $options) ? $options['api_version'] : 'v1';
    }

    /**
     * 设置 base uri
     *
     * @param $baseUri
     *
     * @return $this
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    /**
     * 获取 base uri
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * 设置 api key 和 api secret
     *
     * @param $apiKey
     * @param $apiSecret
     *
     * @return $this
     */
    public function setCustomer($apiKey, $apiSecret)
    {
        $this->apiKey    = $apiKey;
        $this->apiSecret = $apiSecret;

        return $this;
    }

    /**
     * 获取 api key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * 设置 api key
     *
     * @param mixed|string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * 获取 api secret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * 设置 api secret
     *
     * @param mixed|string $apiSecret
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }

    /**
     * 获取 api version
     *
     * @return mixed|string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * 设置 api version
     *
     * @param $apiVersion
     *
     * @return $this
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * 设置超时时间
     *
     * @param $time
     *
     * @return $this
     */
    public function setOutTime($time)
    {
        $this->outTime = $time;

        return $this;
    }

    /**
     * 获取当前设置的超时时间
     *
     * @return float|mixed
     */
    public function getOutTime()
    {
        return $this->outTime;
    }

    /**
     * get a response code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    /**
     * 获取请求的数据
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @return array
     * @throws ResponseDataParseException
     */
    public function getResponseData()
    {
        $responseBody = (string)$this->getResponse()->getBody();

        $this->responseBody = json_decode($responseBody, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new ResponseDataParseException('Failed to parse JSON: ' . json_last_error_msg());
        }

        return $this->responseBody;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface \GuzzleHttp\Psr7\RequestInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface \GuzzleHttp\Psr7\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Client|ClientInterface
     * @throws ApiClientInitException
     */
    public function getHttpClient()
    {
        if (!$this->baseUri) {
            throw new ApiClientInitException('没有配置有效的 BaseUri');
        }
        if (!($this->httpClient instanceof ClientInterface)) {
            $this->httpClient = new Client([
                'base_uri' => rtrim($this->baseUri, '/') . '/',
                'time_out' => $this->outTime,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * Call a Get Request
     *
     * @param string $uri
     * @param array  $query
     *
     * @return mixed|null
     */
    public function get($uri, $query = [])
    {
        return $this->request('GET', $uri, $query);
    }

    /**
     * Call a Post Request
     *
     * @param string $uri
     * @param array  $data
     *
     * @return mixed|null
     */
    public function post($uri, $data = [])
    {
        return $this->request('POST', $uri, $data);
    }

    /**
     * Call a Put Request
     *
     * @param string $uri
     * @param array  $data
     *
     * @return mixed|null
     */
    public function put($uri, $data = [])
    {
        return $this->request('PUT', $uri, $data);
    }

    /**
     * Call a delete Request
     *
     * @param string $uri
     * @param array  $query
     *
     * @return ApiClient
     */
    public function delete($uri, $query = [])
    {
        return $this->request('DELETE', $uri, $query);
    }

    /**
     * Call a patch Request
     *
     * @param string $uri
     * @param array  $data
     *
     * @return ApiClient
     */
    public function patch($uri, $data = [])
    {
        return $this->request('PATCH', $uri, $data);
    }

    /**
     * Call a Request
     *
     * @param string $method
     * @param string $uri
     * @param array  $optionals
     *
     * @return $this
     * @throws \Exception
     */
    public function request($method, $uri, $optionals = [])
    {
        $options = $this->extractOptionalParameters($uri, $optionals);
        $uri     = $this->compileRoute($uri, $optionals);
        $data    = [];
        $method  = strtoupper($method);

        $paramType = $method === 'POST' ? RequestOptions::FORM_PARAMS : RequestOptions::QUERY;
        if (key_exists(RequestOptions::JSON, $options)) {
            $json = $options[RequestOptions::JSON];
            unset($options[RequestOptions::JSON]);
            $data[RequestOptions::JSON] = $json;
            $paramType                  = RequestOptions::QUERY;
        }
        $data[$paramType]               = $this->getAuthParams($options);
        $this->requestData              = $data;
        $data[RequestOptions::ON_STATS] = function (TransferStats $stats) {
            $this->lastUrl      = (string) $stats->getEffectiveUri();
            $this->request      = $stats->getRequest();
            $this->transferTime = $stats->getTransferTime();
        };
        if ($this->log) {
            $data['handler'] = $this->handlerStack;
        }
        if ($this->apiVersion) {
            $uri = $this->apiVersion . $uri;
        }
        $promise = $this->getHttpClient()->requestAsync($method, $uri, $data);
        $promise->then(
            function (ResponseInterface $response) {
                $this->response = $response;
                if ($this->completeCallBack) {
                    call_user_func($this->completeCallBack);
                }
            },
            function ($e) {
                if ($e instanceof BadResponseException) {
                    $this->request  = $e->getRequest();
                    $this->response = $e->getResponse();
                } elseif ($e instanceof RequestException) {
                    $this->request = $e->getRequest();
                    if ($e->hasResponse()) {
                        $this->response = $e->getResponse();
                    }
                }
                throw $e;
            }
        );
        $promise->wait();

        return $this;
    }

    /**
     * 获取最后一次请求的 URL
     *
     * @return string
     */
    public function getLastUrl()
    {
        return (string)$this->lastUrl;
    }

    /**
     * 获取请求时间
     *
     * @return double
     */
    public function getTransferTime()
    {
        return $this->transferTime;
    }

    /**
     * 设置日志
     *
     * @param LoggerInterface $logger
     * @param mixed           $format
     */
    public function setLogger(LoggerInterface $logger, $format = null)
    {
        $this->log          = true;
        $this->handlerStack = HandlerStack::create();
        if (is_array($format)) {
            foreach ($format as $item) {
                $this->handlerStack->unshift($this->createGuzzleLoggingMiddleware($logger, $item));
            }
        } else {
            $this->handlerStack->push($this->createGuzzleLoggingMiddleware($logger, $format));
        }
    }

    /**
     * 创建一个日志中间件
     *
     * @param $logger
     * @param $Format
     *
     * @return callable
     */
    private function createGuzzleLoggingMiddleware($logger, $Format)
    {
        return Middleware::log($logger, new MessageFormatter($Format));
    }

    /**
     * 将所有请求参数转为字符串
     *
     * @param array $param
     *
     * @return array
     */
    public function standardizeParam($param)
    {
        return array_map(function ($item) {
            if (is_array($item)) {
                return $this->standardizeParam($item);
            } else {
                return (string)$item;
            }
        }, $param);
    }

    /**
     * 获取 auth param
     *
     * @param array $options
     *
     * @return mixed
     */
    private function getAuthParams($options)
    {
        $this->validateApiOptions();

        if (!$this->authenticator instanceof Authenticator) {
            $this->authenticator = new Authenticator($this->apiKey, $this->apiSecret);
        }

        $options = $this->standardizeParam($options);

        return $this->authenticator->getAuthParams($options);
    }

    /**
     * 验证 api 必须的是否已经被初始化
     *
     * @throws ApiClientInitException
     */
    private function validateApiOptions()
    {
        if (!$this->apiKey) {
            throw new ApiClientInitException('没有配置有效的 apiKey');
        }
        if (!$this->apiSecret) {
            throw new ApiClientInitException('没有配置有效的 apiSecret');
        }
    }

    /**
     * 替换 url 中的变量
     *
     * @param string $uri
     * @param array  $optionals
     *
     * @return string
     */
    protected function compileRoute($uri, $optionals)
    {
        return preg_replace_callback('/\{(\w+?)\??\}/', function ($matches) use ($optionals) {
            return isset($optionals[$matches[1]]) ? $optionals[$matches[1]] : '';
        }, $uri);
    }

    /**
     * 去掉 uri 中已存在的变量
     *
     * @param string $uri
     * @param array  $optionals
     *
     * @return array
     */
    protected function extractOptionalParameters($uri, $optionals)
    {
        preg_match_all('/\{(\w+?)\??\}/', $uri, $matches);

        return array_except($optionals, $matches[1]);
    }
}
