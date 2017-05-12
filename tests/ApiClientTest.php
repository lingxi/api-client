<?php

namespace Lingxi\WechatPusherApiClient\Test;

use GuzzleHttp\Psr7\Response;
use Lingxi\WechatPusherApiClient\WechatPusherApiClient;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    const API_BASE_URL   = 'http://wxpusherdebug1109.lxi.me/';
    const API_KEY        = 'pj5iHdpka';
    const API_SECRET     = '7b8b1fe049494f78a555b3c7c6498b8b';
    const API_VERSION_V1 = '';
    const API_VERSION_V2 = 'v2';
    protected $apiClient;

    protected function setUp()
    {
        $this->apiClient = new WechatPusherApiClient(self::API_KEY, self::API_SECRET, self::API_BASE_URL);
        $this->apiClient->setApiVersion(self::API_VERSION_V1)->setApiOutTime(3.0);
    }

    public function testGetAuthorizedAccounts()
    {
        $data = $this->apiClient->getAuthorizedAccounts();
        echo (json_encode($data));die;
        $this->assertTrue(is_array($data));
    }

    public function testGetLastUrl()
    {
        $urlReg = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
        $this->apiClient->getAuthorizedAccounts();
        $this->assertRegExp($urlReg, $this->apiClient->getLastUrl());
    }

    public function testGetResponse()
    {
        $this->apiClient->getAuthorizedAccounts();
        $this->assertInstanceOf(Response::class, $this->apiClient->getResponse());
    }
}
