<?php

namespace App\OfferBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryOfferDescriptionControllerTest extends WebTestCase
{
    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/backoffice/offers/description/add');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/backoffice/offers/description/edit');
    }

    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/backoffice/offers/descriptions');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/backoffice/offers/description/delete');
    }

}
