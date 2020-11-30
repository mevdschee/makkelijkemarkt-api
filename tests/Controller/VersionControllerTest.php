<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VersionControllerTest extends WebTestCase
{
    public function testGetVersion()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/version/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ]);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'Status code is 200');
        $this->assertEquals('application/json', $response->headers->get('Content-Type', ''), 'Content type is JSON');
        $this->assertEquals('1.1.0', json_decode($response->getContent(), true)['apiVersion'], 'API version is 1.1.0');
    }
}
