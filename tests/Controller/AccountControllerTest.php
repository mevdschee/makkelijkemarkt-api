<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    public function testGetAccount()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/account/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ]);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful(), 'Request is successful');
        $this->assertNotFalse(json_decode($response->getContent()), 'JSON is valid');
    }

}
