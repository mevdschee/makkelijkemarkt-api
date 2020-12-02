<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

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
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Syntax error'], $result);
    }

    public function testLoginWhoamiOnlyAppKey()
    {
        $client = static::createClient();
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_USER")'], $result);
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
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Account unknown'], $result);
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
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Password invalid'], $result);
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
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Account is locked'], $result);
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
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Account is not active'], $result);
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
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Account is locked'], $result);
    }

    public function testLoginLogout()
    {
        $client = static::createClient();

        // first request
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account1@amsterdam.nl",
            'password' => "Password1!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);
        $uuid = $result['uuid'];

        // second request
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);

        // third request
        $client->request('GET', '/api/1.1.0/logout/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);

        // fourth request
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Invalid token time'], $result);
    }

    public function testBasicId()
    {
        $client = static::createClient();

        // first request
        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account1@amsterdam.nl",
            'password' => "Password1!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals('account1@amsterdam.nl', $result['account']['username']);
        $accountId = $result['account']['id'];

        // second request
        $client->request('POST', '/api/1.1.0/login/basicId/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'accountId' => $accountId,
            'password' => "Password1!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(36, strlen($result['uuid']));
    }
}
