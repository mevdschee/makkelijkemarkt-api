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

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    public function getTokenUuid(KernelBrowser $client, string $role): string
    {
        $roles = ['ROLE_USER' => 1, 'ROLE_SENIOR' => 5, 'ROLE_ADMIN' => 3];
        $i = $roles[$role];

        $client->request('POST', '/api/1.1.0/login/basicUsername/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
        ], json_encode([
            'username' => "account$i@amsterdam.nl",
            'password' => "Password$i!",
        ]));
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertTrue($response->isSuccessful(), 'Request has succeeded');
        $this->assertEquals($role, $result['account']['roles'][0]);
        return $result['uuid'];
    }
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
        $this->assertEquals(['error' => 'Access Denied by controller annotation @IsGranted("ROLE_SENIOR")'], $result);
    }

    public function testGetAccountAsSeniorNonExisting()
    {
        $client = static::createClient();
        $uuid = $this->getTokenUuid($client, 'ROLE_SENIOR');
        $client->request('GET', "/api/1.1.0/account/0", [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertEquals(['error' => 'No account with id 0'], $result);
    }

}
