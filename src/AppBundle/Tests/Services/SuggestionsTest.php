<?php

namespace AppBundle\Tests\Services;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Services\Books\RatingService;
use AppBundle\Entity\User;
use AppBundle\Entity\Book;

class SuggestionsTest extends WebTestCase
{
    public static $container;
    public static $doctrine;
    public static $ratingService;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadBookData',
        'AppBundle\DataFixtures\ORM\LoadBookRatingData'
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
         self::$ratingService = self::$container->get('app.suggestion');
    }

    public function testBookSuggestions()
    {
        $this->loadFixtures(self::$fixtures);

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test3');
        $suggestions = self::$ratingService->getSuggestionsFromBook($book);

        $this->assertEquals(2,count($suggestions));
    }

    public function testUserSuggestions()
    {
        $this->loadFixtures(self::$fixtures);

        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $suggestions = self::$ratingService->getSuggestionsFromUser($user);

        $this->assertEquals(2,count($suggestions));
    }

    public function testUserBookSuggestions()
    {
        $this->loadFixtures(self::$fixtures);

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test3');
        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $suggestions = self::$ratingService->getSuggestionsFromBookAndUser($book,$user);

        $this->assertEquals(2,count($suggestions));
    }
}

