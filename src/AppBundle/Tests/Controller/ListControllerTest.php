<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ListControllerTest extends WebTestCase
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

    public function testAddToNewList(){
        static::setUpBeforeClass();

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');
        $formData = array(
            'book' => $book->getId(),
            'newName' => 'Prueba lista',
        );

        $this->client->request('POST', '/add_to_list', $formData);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAddToList(){
        static::setUpBeforeClass();

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test3');
        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');

        $formData = array(
            'book' => $book->getId(),
            'list' => $list->getId(),
        );

        $this->client->request('POST', '/add_to_list', $formData);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');
        $this->assertEquals(3,$list->getBooks()->count());
    }

    public function testRemoveFromList(){
        static::setUpBeforeClass();

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');

        self::$doctrine->getManager()->flush();
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');
        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');

        $formData = array(
            'book' => $book->getId(),
            'list' => $list->getId()
        );

        $this->client->request('DELETE', '/remove_from_list', $formData);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');
        $this->assertEquals(1,$list->getBooks()->count());
    }

    public function testListPage(){
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $this->client->request('GET', '/lista/list1');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testMyListsPage(){
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $this->client->request('GET', '/my_lists');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testPopularsLists(){
        $this->client = static::createClient();
        $this->client->request('GET', '/popular_lists_timeline');
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
