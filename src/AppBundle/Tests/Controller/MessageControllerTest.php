<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MessageControllerTest extends WebTestCase
{
    private $client = null;
    public static $container;
    public static $doctrine;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadMessageData'
    );

    public static function setUpBeforeClass()
    {
         //start the symfony kernel
         $kernel = static::createKernel();
         $kernel->boot();

         //get the DI container
         self::$container = $kernel->getContainer();
         //now we can instantiate our service (if you want a fresh one for
         //each test method, do this in setUp() instead
         self::$doctrine = self::$container->get('doctrine');
    }

    public function testMessageFail(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $user1 = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $this->doLogin($this->client,'test2','test');
        $crawler = $this->client->request('GET','/mensajes');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertEquals(0,$crawler->filter('li.js-message-entry')->count());

        $formData = array(
            'to' => $user1->getId(),
            'text' => 'Prueba menasje invalida',
        );

        $this->client->request('POST', '/post_message', $formData);
        $this->assertEquals(
            403,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testMessageOk(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $user1 = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $this->doLogin($this->client,'test3','test');
        $crawler = $this->client->request('GET','/mensajes');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertEquals(2,$crawler->filter('li.conversacion-entry')->count());

        $formData = array(
            'to' => $user1->getId(),
            'text' => 'Prueba mensaje válida',
        );

        $this->client->request('POST', '/post_message', $formData);
        $this->assertEquals(
            201,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testConversation(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $user1 = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $this->doLogin($this->client,'test3','test');
        $crawler = $this->client->request('GET','/mensajes/'.$user1->getId());

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertEquals(2,$crawler->filter('li.js-message-entry')->count());

        $formData = array(
            'to' => $user1->getId(),
            'text' => 'Prueba mensaje válida',
        );

        $this->client->request('POST', '/post_message', $formData);
        $this->assertEquals(
            201,
            $this->client->getResponse()->getStatusCode()
        );

        $crawler = $this->client->request('GET','/mensajes/'.$user1->getId());

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertEquals(3,$crawler->filter('li.js-message-entry')->count());
    }

    public function doLogin($client,$username, $password) {
       $crawler = $client->request('GET', '/login');
       $form = $crawler->selectButton('_submit')->form(array(
           '_username'  => $username,
           '_password'  => $password,
           ));
       $client->submit($form);

       $this->assertTrue($this->client->getResponse()->isRedirect());

       $crawler = $client->followRedirect();
    }
}
