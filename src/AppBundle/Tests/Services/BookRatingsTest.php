<?php

namespace AppBundle\Tests\Services;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Services\Books\RatingService;
use AppBundle\Entity\User;
use AppBundle\Entity\Book;

class BookRatingsTest extends WebTestCase
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
         self::$ratingService = self::$container->get('app.rating');
    }

    public function testFirstRating()
    {
        $this->loadFixtures(self::$fixtures);

        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $this->assertEquals(5,$book->getCachedRate());

        self::$ratingService->newBookRating($user,$book,5);

        self::$doctrine->getManager()->flush();

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');
        $ratings = self::$doctrine->getRepository('AppBundle:BookRating')->findAll();

        $this->assertEquals(6,count($ratings));
        $this->assertEquals(7*0.8,$book->getCachedRate());
    }

    public function testTwoRating()
    {
        $this->loadFixtures(self::$fixtures);

        $user1 = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $user2 = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test2');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        self::$ratingService->newBookRating($user1,$book,5);
        self::$doctrine->getManager()->flush();

        self::$ratingService->newBookRating($user2,$book,0);
        self::$doctrine->getManager()->flush();

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');
        $ratings = self::$doctrine->getRepository('AppBundle:BookRating')->findAll();

        $this->assertEquals(7,count($ratings));
        //$this->assertEquals(2,$book->getRatings()->count());
        $this->assertEquals(5*0.8,round($book->getCachedRate()));
    }

    public function testRatingChange()
    {
        $this->loadFixtures(self::$fixtures);

        $user = self::$doctrine->getRepository('AppBundle:User')->findOneByUsername('test1');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        self::$ratingService->newBookRating($user,$book,5);
        self::$doctrine->getManager()->flush();

        self::$ratingService->newBookRating($user,$book,3);
        self::$doctrine->getManager()->flush();

        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');
        $ratings = self::$doctrine->getRepository('AppBundle:BookRating')->findAll();

        $this->assertEquals(6,count($ratings));

        //$this->assertEquals(1,$book->getRatings()->count());
        $this->assertEquals(6*0.8,$book->getCachedRate());
    }
}
