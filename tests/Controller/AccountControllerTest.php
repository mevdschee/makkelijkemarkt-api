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

use App\Tests\Base\LoginWebTestCase;

class AccountControllerTest extends LoginWebTestCase
{
    public function testListAccountsAsUser()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_USER');
        $client->request('GET', '/api/1.1.0/account/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_SENIOR")'], $result);
    }

    public function testListAccountsAsSenior()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_SENIOR');
        $client->request('GET', '/api/1.1.0/account/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('account0@amsterdam.nl', $result[0]['username']);
        $this->assertEquals('account1@amsterdam.nl', $result[1]['username']);
        $this->assertEquals('account2@amsterdam.nl', $result[2]['username']);
        $this->assertEquals('account3@amsterdam.nl', $result[3]['username']);
    }

    public function testGetAccountAsUser()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_USER');
        $client->request('GET', "/api/1.1.0/account/0", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_SENIOR")'], $result);
    }

    public function testGetNonExistingAccountAsSenior()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_SENIOR');
        $client->request('GET', "/api/1.1.0/account/0", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'No account with id 0'], $result);
    }

    public function testGetExistingAccountAsSenior()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_SENIOR');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('GET', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals($accountId, $result['id']);
    }

    public function testEditAccountAsSenior()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_SENIOR');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_ADMIN")'], $result);
    }

    public function testEditAccountAsAdminWithoutName()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field naam is missing'], $result);
    }

    public function testEditAccountAsAdminWithoutEmail()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field email is missing'], $result);
    }

    public function testEditAccountAsAdminWithoutUsername()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
            'email' => 'account3@amsterdam.nl',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field username is missing'], $result);
    }

    public function testEditAccountAsAdminWithInvalidRole()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
            'email' => 'account3@amsterdam.nl',
            'username' => 'account3@amsterdam.nl',
            'role' => 'ROLE_UNKNOWN_ROLE',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Unknown role'], $result);
    }

    public function testEditAccountAsAdminWithInvalidAccountId()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('PUT', "/api/1.1.0/account/0", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
            'email' => 'account3@amsterdam.nl',
            'username' => 'account3@amsterdam.nl',
            'role' => 'ROLE_ADMIN',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'No account with id 0'], $result);
    }

    public function testEditAccountAsAdminWithoutActive()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
            'email' => 'account3@amsterdam.nl',
            'username' => 'account3@amsterdam.nl',
            'role' => 'ROLE_ADMIN',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field active is missing'], $result);
    }

    public function testEditAccountAsAdminWithoutPassword()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
            'email' => 'account3@amsterdam.nl',
            'username' => 'account3@amsterdam.nl',
            'role' => 'ROLE_ADMIN',
            'active' => true,
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals('account3@amsterdam.nl', $result['username']);
    }

    public function testEditAccountAsAdminWithPassword()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $accountId = $this->getAccountProperty($client, $uuid, 'id');
        $client->request('PUT', "/api/1.1.0/account/$accountId", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => 'Account3',
            'email' => 'account3@amsterdam.nl',
            'username' => 'account3@amsterdam.nl',
            'role' => 'ROLE_ADMIN',
            'active' => true,
            'password' => 'Password3!',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals('account3@amsterdam.nl', $result['username']);
    }

    public function testCreateNewAccountAsSenior()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_SENIOR');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'username' => "new.account@amsterdam.nl",
            'password' => "NewPassword!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_ADMIN")'], $result);
    }

    public function testCreateNewAccountAsAdminWithoutName()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field naam is missing'], $result);
    }

    public function testCreateNewAccountAsAdminWithoutEmail()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "NewAccount",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field email is missing'], $result);
    }

    public function testCreateNewAccountAsAdminWithoutUsername()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "NewAccount",
            'email' => "new.account@amsterdam.nl",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field username is missing'], $result);
    }

    public function testCreateNewAccountAsAdminWithoutPassword()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "NewAccount",
            'email' => "new.account@amsterdam.nl",
            'username' => "new.account@amsterdam.nl",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field password is missing'], $result);
    }

    public function testCreateNewAccountAsAdminWithoutRole()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "NewAccount",
            'email' => "new.account@amsterdam.nl",
            'username' => "new.account@amsterdam.nl",
            'password' => "NewPassword!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Required field role is missing'], $result);
    }

    public function testCreateNewAccountAsAdminWithInvalidRole()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "NewAccount",
            'email' => "new.account@amsterdam.nl",
            'username' => "new.account@amsterdam.nl",
            'password' => "NewPassword!",
            'role' => 'ROLE_UNKNOWN_ROLE',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'Unknown role'], $result);
    }

    public function testCreateDuplicateAccountAsAdmin()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "Account0",
            'email' => "account0@amsterdam.nl",
            'username' => "account0@amsterdam.nl",
            'password' => "Password0!",
            'role' => 'ROLE_USER',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals(['error' => 'User already exists'], $result);
    }

    public function testCreateNewAccountAsAdmin()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_ADMIN');
        $time = microtime(true);
        $client->request('POST', "/api/1.1.0/account/", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ], json_encode([
            'naam' => "AccountTime",
            'email' => "$time@amsterdam.nl",
            'username' => "$time@amsterdam.nl",
            'password' => "PasswordTime!",
            'role' => 'ROLE_USER',
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertEquals("$time@amsterdam.nl", $result['username']);
    }

}
