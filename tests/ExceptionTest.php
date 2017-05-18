<?php

namespace Lingxi\ApiClient\Test;

use Lingxi\ApiClient\ApiClient;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    const API_BASE_URL = 'http://api.lingxi360.com';
    const API_KEY      = 'pj5iHdpka';
    const API_SECRET   = '7b8b1fe049494f78a555b3c7c6498b8b';

    /**
     * @expectedException \Lingxi\ApiClient\Exceptions\ApiClientInitException
     */
    public function testApiClientInitException()
    {
        $apiClient = new ApiClient();
        $apiClient->getHttpClient();
        $apiClient->get('/test/fun');
        $apiClient->setApiKey('111');
        $apiClient->get('/test/fun');
    }

    public function test()
    {
        
    }
}
