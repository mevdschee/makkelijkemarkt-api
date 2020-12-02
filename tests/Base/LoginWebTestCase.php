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

namespace App\Tests\Base;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginWebTestCase extends WebTestCase
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
        $this->assertNotNull($result);
        $this->assertTrue($response->isSuccessful(), 'Request has succeeded');
        $this->assertEquals($role, $result['account']['roles'][0]);
        return $result['uuid'];
    }

    public function getAccountProperty(KernelBrowser $client, string $uuid, string $key): string
    {
        $client->request('GET', '/api/1.1.0/login/whoami/', [], [], [
            'HTTP_MmAppKey' => 'testkey',
            'HTTP_Authorization' => "Bearer $uuid",
        ]);
        $response = $client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertNotNull($result);
        $this->assertArrayHasKey($key, $result['account']);
        return $result['account'][$key];
    }
}
