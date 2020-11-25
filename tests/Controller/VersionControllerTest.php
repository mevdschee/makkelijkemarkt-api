<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VersionControllerTest extends WebTestCase
{
    public function testGetVersion()
    {
        $client = static::createClient();
        $client->request('GET', '/1.1.0/version/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'Status code is 200');
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Content type is JSON');
        $this->assertEquals('1.1.0', json_decode($response->getContent(), true)['apiVersion'], 'API version is 1.1.0');
    }
}
