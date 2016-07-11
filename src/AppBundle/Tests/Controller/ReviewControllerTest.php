<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ReviewControllerTest extends WebTestCase
{

    private $client = null;
    public static $container;
    public static $doctrine;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadBookData',
        'AppBundle\DataFixtures\ORM\LoadReviewData',
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

    public function testReviewSubmit()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogIn('test1','test');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $formData = array(
            'book' => $book->getId(),
            'title' => 'Prueba review',
            'text' => 'Prueba review',
            'spoiler' => 1,
        );

        $this->client->request('POST', '/review', $formData);
        $this->assertEquals(
            201,
            $this->client->getResponse()->getStatusCode()
        );

        $this->client->request('GET', '/ajaxbookreviews/'.$book->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $this->client->request('GET', '/bookreviews/'.$book->getId());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testReviewPage(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $review = self::$doctrine->getRepository('AppBundle:Review')->findOneByTitle('Review1');

        $this->client->request('GET', '/libro/'.$review->getBook()->getSlug().'/resena/'.$review->getId());
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
