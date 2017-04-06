<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGetProducts()
    {
        $client = static::createClient();

        $client->request('GET', '/products');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProduct()
    {
        $client = static::createClient();

        $client->request('GET', '/products/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}