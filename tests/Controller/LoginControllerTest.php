<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testBasicUsernameWithoutAuthorizationHeader()
    {
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ]);
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Syntax error'], json_decode($response->getContent(), true));
    }

    public function testLoginWhoamiOnlyAppKey()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ]);
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_USER")'], json_decode($response->getContent(), true));
    }

    public function testLoginWithInvalidUsername()
    {
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "unknown@amsterdam.nl",
            'password' => "Password1!",
        ]));
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Account unknown'], json_decode($response->getContent(), true));
    }

    public function testLoginWithInvalidPassword()
    {
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account1@amsterdam.nl",
            'password' => "Unknown",
        ]));
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Password invalid'], json_decode($response->getContent(), true));
    }

    public function testLoginWithLockedAccount()
    {
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account2@amsterdam.nl",
            'password' => "Password2!",
        ]));
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Account is locked'], json_decode($response->getContent(), true));
    }

    public function testLoginWithInactiveAccount()
    {
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account7@amsterdam.nl",
            'password' => "Password7!",
        ]));
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Account is not active'], json_decode($response->getContent(), true));
    }

    public function testLoginWithLockedInactiveAccount()
    {
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account6@amsterdam.nl",
            'password' => "Password6!",
        ]));
        $response = $client->getResponse();

        $this->assertFalse($response->isSuccessful(), 'Request has failed');
        $this->assertEquals(['error' => 'Account is locked'], json_decode($response->getContent(), true));
    }

    public function testLoginLogout()
    {
        // first request
        $client = static::createClient();
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account1@amsterdam.nl",
            'password' => "Password1!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertTrue($response->isSuccessful(), 'Request has succeeded');
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);
        $uuid = $result['uuid'];
        // second request
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertTrue($response->isSuccessful(), 'Request has succeeded');
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);
        // third request
        $client->request('GET', '/api/1.1.0/logout/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertTrue($response->isSuccessful(), 'Request has succeeded');
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);
        // fourth request
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertFalse($response->isSuccessful(), 'Request has succeeded');
        $this->assertEquals(['error' => 'Invalid token time'], $result);
    }

}
