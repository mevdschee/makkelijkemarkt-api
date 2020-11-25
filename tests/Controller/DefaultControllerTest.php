<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/1.1.0/');
        $response = $client->getResponse();

        $this->assertEquals(['msg' => 'Hallo!'], json_decode($response->getContent(), true));
    }
}
