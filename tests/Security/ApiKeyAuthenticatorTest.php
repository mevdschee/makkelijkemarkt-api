<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiKeyAuthenticatorTest extends WebTestCase
{
    public function testWithoutMmAppKey()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/account/');
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals('Invalid application key', json_decode($response->getContent(), true));
    }

    public function testWithoutValidAuthorizationHeader()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/account/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => 'NotBearer test-uuid',
        ]);
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals('Invalid authorization header', json_decode($response->getContent(), true));
    }

    public function testWithoutUuid()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/account/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => 'Bearer',
        ]);
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals('Invalid token uuid', json_decode($response->getContent(), true));
    }

    public function testWithoutValidUuid()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/account/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => 'Bearer test-uuid',
        ]);
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals('Invalid token', json_decode($response->getContent(), true));
    }

}
