<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BookRelationControllerTest extends WebTestCase
{
    private $client = null;
    public static $container;
    public static $doctrine;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadBookData',
        'AppBundle\DataFixtures\ORM\LoadBookRatingData',
        'AppBundle\DataFixtures\ORM\LoadListData'
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

    public function testAddReading(){
        static::setUpBeforeClass();

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $formData = array(
            'book' => $book->getId(),
        );

        $this->client->request('POST', '/user_add_reading', $formData);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAddRead(){
        static::setUpBeforeClass();

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $formData = array(
            'book' => $book->getId(),
        );

        $this->client->request('POST', '/user_add_read', $formData);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAddWant(){
        static::setUpBeforeClass();

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $formData = array(
            'book' => $book->getId(),
        );

        $this->client->request('POST', '/user_add_want', $formData);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testPopularsBooks(){
        $this->client = static::createClient();
        $this->client->request('GET', '/popular_books_timeline');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testPopularsAuthors(){
        $this->client = static::createClient();
        $this->client->request('GET', '/popular_authors_timeline');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function doLogin($username, $password) {
       $crawler = $this->client->request('GET', '/login');
       $form = $crawler->selectButton('_submit')->form(array(
           '_username'  => $username,
           '_password'  => $password,
           ));
       $this->client->submit($form);

       $this->assertTrue($this->client->getResponse()->isRedirect());

       $crawler = $this->client->followRedirect();
    }
}

