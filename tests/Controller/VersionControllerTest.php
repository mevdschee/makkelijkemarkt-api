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
