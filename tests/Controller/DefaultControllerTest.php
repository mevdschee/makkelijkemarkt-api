<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(['msg' => 'Hallo!'], json_decode($response->getContent(), true));
    }
}
