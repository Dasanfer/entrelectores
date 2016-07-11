<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PollControllerTest extends WebTestCase
{
    private $client = null;
    public static $container;
    public static $doctrine;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadBookData',
        'AppBundle\DataFixtures\ORM\LoadBookRatingData',
        'AppBundle\DataFixtures\ORM\LoadListData',
        'AppBundle\DataFixtures\ORM\LoadPollData'
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

    public function testPollList(){
        static::setUpBeforeClass();
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();

        $this->client->request('GET', '/encuestas');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testPollData(){
        static::setUpBeforeClass();
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();

        $this->client->request('GET', '/poll_data/title-poll');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testPollVote(){
        static::setUpBeforeClass();
        $this->loadFixtures(self::$fixtures);

        $option = self::$doctrine->getRepository('AppBundle:PollOption')->findOneByTitle('title1');
        $this->client = static::createClient();

        $this->client->request('POST', '/poll_vote',array('option' => $option->getId()));
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
        $answers = self::$doctrine->getRepository('AppBundle:PollAnswer')->findAll();

        $this->assertEquals(1,count($answers));
    }
}
