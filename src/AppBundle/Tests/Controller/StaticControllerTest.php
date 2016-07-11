<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class StaticControllerTest extends WebTestCase
{
    public function testStaticPages(){

        $client = static::createClient();
        $crawler = $client->request('GET', '/prensa');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $crawler = $client->request('GET', '/contacta');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $crawler = $client->request('GET', '/libros-promocionados');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $formData = array(
            'nombre' => 'automatic test',
            'libro' => 'automatic test',
            'email' => 'testing@clouddistrict.com',
            'program' => 'promo'
        );

        $crawler = $client->request('POST', '/libros-promocionados',$formData);
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $crawler = $client->request('GET', '/terminos-y-condiciones');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }
}
