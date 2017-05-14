<?php

namespace Lingxi\ApiClient\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Lingxi\ApiClient\ApiClient;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    const API_BASE_URL = 'http://api.lingxi360.com';
    const API_KEY      = 'pj5iHdpka';
    const API_SECRET   = '7b8b1fe049494f78a555b3c7c6498b8b';
    protected $apiClient;

    protected function setUp()
    {
        $option          = [
            'base_uri'   => self::API_BASE_URL,
            'api_key'    => self::API_KEY,
            'api_secret' => self::API_SECRET,
        ];
        $this->apiClient = new ApiClient($option);
    }

    public function testGetterAndSetter()
    {
        $apiClient = new ApiClient;
        $apiClient->setBaseUri(self::API_BASE_URL);
        $this->assertEquals(self::API_BASE_URL, $apiClient->getBaseUri());
        $apiClient->setCustomer(self::API_KEY, self::API_SECRET);
        $this->assertEquals(self::API_KEY, $apiClient->getApiKey());
        $this->assertEquals(self::API_SECRET, $apiClient->getApiSecret());
        $apiClient->setApiVersion('v3');
        $this->assertEquals('v3', $apiClient->getApiVersion());
        $apiClient->setOutTime(5.0);
        $this->assertEquals(5.0, $apiClient->getOutTime());
        $apiClient->setApiKey('11111');
        $this->assertEquals('11111', $apiClient->getApiKey());
        $apiClient->setApiSecret('22222');
        $this->assertEquals('22222', $apiClient->getApiSecret());
    }

    public function testGetLastUrl()
    {
        $urlReg = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
        $this->apiClient->get('/test/fun');
        $this->assertRegExp($urlReg, $this->apiClient->getLastUrl());
    }

    public function testGetResponse()
    {
        $this->apiClient->get('/test/fun');
        $this->assertInstanceOf(Response::class, $this->apiClient->getResponse());
    }

    public function testGetHttpClient()
    {
        $this->apiClient->get('/test/fun');
        $this->assertInstanceOf(Client::class, $this->apiClient->getHttpClient());
    }

    public function testGetResponseCode()
    {
        $this->apiClient->get('/test/fun');
        $this->assertEquals(200, $this->apiClient->getResponseCode());
    }

    public function testGetResponseData()
    {
        $this->assertArrayHasKey('msg', $this->apiClient->get('/test/fun')->getResponseData());
    }

    public function testRouteParam()
    {
        $query = [
            'test' => 'test',
        ];
        $this->assertArrayHasKey('msg', $this->apiClient->get('/{test}/fun', $query)->getResponseData());
    }

    public function testStandardizeParam()
    {
        $options = [
            'contact_id' => 1000,
            'from'       => 'contact',
            'to'         => [
                'cc' => '100',
                'd'  => 10000,
            ],
        ];
        $options = $this->apiClient->standardizeParam($options);
        collect($options)->each(function ($key, $value) {
            $this->assertTrue(is_string($value));
        });
    }

    public function testGet()
    {

    }

    public function testPost()
    {

    }

    public function testPut()
    {

    }

    public function testHead()
    {

    }

    public function testOptions()
    {

    }

    public function testPatch()
    {

    }
}
