<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client;

class UserControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = new Client([
            'base_uri' => 'http://127.0.0.1:8000'
        ]);
    }

    public function testGetUsers()
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetUser()
    {
        $client = static::createClient();

        $client->request('GET', '/users/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('username', $client->getResponse()->getContent());
        $this->assertContains('id', $client->getResponse()->getContent());
    }

    public function testPostUsers()
    {
        $response = $this->client->post('/users', [
            'json' => [
                'email' => 'vivi@vivi.fr',
                'username' => 'vivi',
                'plainPassword' => [
                    'first' => 'vivi',
                    'second' => 'vivi'
                ]
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}