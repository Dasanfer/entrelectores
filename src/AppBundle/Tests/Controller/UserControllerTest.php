<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use AppBundle\Entity\FollowEvent;
use AppBundle\Entity\Comment;

class UserControllerTest extends WebTestCase
{

    private $client = null;
    public static $container;
    public static $doctrine;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadBookData',
        'AppBundle\DataFixtures\ORM\LoadListData',
        'AppBundle\DataFixtures\ORM\LoadEventsData'
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

    public function testBookFollow()
    {
        $this->loadFixtures(self::$fixtures);

        $this->client = static::createClient();
        $this->doLogin('test3','test');

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $crawler = $this->client->request('POST', '/follow_element/book/'.$book->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');
        $this->assertEquals(1,$book->getFollowers()->count());

        $crawler = $this->client->request('POST', '/unfollow_element/book/'.$book->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $this->assertEquals(0,$user->getBooksFollowed()->count());
    }

    public function testAuthorFollow()
    {
        $this->loadFixtures(self::$fixtures);

        $this->client = static::createClient();
        $this->doLogin('test3','test');

        $author = self::$doctrine->getRepository('AppBundle:Author')->findOneBySlug('author1');

        $crawler = $this->client->request('POST', '/follow_element/author/'.$author->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $author = self::$doctrine->getRepository('AppBundle:Author')->findOneBySlug('author1');
        $this->assertEquals(1,$author->getFollowers()->count());

        $crawler = $this->client->request('POST', '/unfollow_element/author/'.$author->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $this->assertEquals(0,$user->getAuthorsFollowed()->count());
    }

    public function testBookListFollow()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test3','test');

        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');

        $crawler = $this->client->request('POST', '/follow_element/list/'.$list->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
        self::$doctrine->getManager()->flush();
        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');
        $this->assertEquals(2,$list->getFollowers()->count());

        $crawler = $this->client->request('POST', '/unfollow_element/list/'.$list->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        self::$doctrine->getManager()->flush();
        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $this->assertEquals(0,$user->getListsFollowed()->count());
    }

    public function testHomepage()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $crawler = $this->client->request('GET', '/user_homepage');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testProfile(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $crawler = $this->client->request('GET', '/user/test1');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $crawler = $this->client->request('GET', '/user/test2');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

    }

    public function testProfileEdit(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $crawler = $this->client->request('GET', '/user_profile_edit');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testUserSearch()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $crawler = $this->client->request('GET', '/listaUsuarios');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(2,$crawler->filter('div.js-search-result')->count());
        $crawler = $this->client->request('GET', '/listaUsuarios?search=test1');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(1,$crawler->filter('div.js-search-result')->count());
    }

    public function testPopularUsers()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/popular_users_timeline');
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
