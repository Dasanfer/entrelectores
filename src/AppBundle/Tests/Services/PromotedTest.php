<?php

namespace AppBundle\Tests\Services;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Services\Books\RatingService;
use AppBundle\Entity\User;
use AppBundle\Entity\Book;

class PromotedTest extends WebTestCase
{
    public static $container;
    public static $doctrine;
    public static $promotedService;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadBookData',
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
         self::$promotedService = self::$container->get('app.promoted');
    }

    public function testPromoted()
    {
        $this->loadFixtures(self::$fixtures);

        $books = self::$promotedService->getPromoted();

        $this->assertEquals(2,count($books));
        $this->assertTrue($books[0]->getPromoted());

        $books = self::$promotedService->getPromoted(2);
        $this->assertEquals(2,count($books));
        $this->assertTrue($books[1]->getPromoted());
        $this->assertTrue($books[0]->getPromoted());

        //there are only 2 promoted
        $books = self::$promotedService->getPromoted(3);
        $this->assertEquals(2,count($books));
        $this->assertTrue($books[1]->getPromoted());
        $this->assertTrue($books[0]->getPromoted());

    }
}

