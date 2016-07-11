<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CommentControllerTest extends WebTestCase
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

    public function testBookCommentSubmit()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();

        $this->doLogin('test1','test');
        $book = self::$doctrine->getRepository('AppBundle:Book')->findOneByTitle('test1');

        $formData = array(
            'book' => $book->getId(),
            'content' => 'Prueba comentario'
        );

        $this->client->request('POST', '/post_comment', $formData);
        $response = $this->client->getResponse();
        $this->assertEquals(
            201,
            $response->getStatusCode()
        );

        $responseContent = json_decode($this->client->getResponse()->getContent());

        $formData = array(
            'book' => $book->getId(),
            'content' => 'Prueba comentario',
            'parent' => $responseContent->id
        );

        $this->client->request('POST', '/post_comment', $formData);

        $this->assertEquals(
            201,
            $this->client->getResponse()->getStatusCode()
        );

        $this->client->request('GET', '/element_conversation/book/'.$book->getId());

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testListCommentSubmit()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();

        $this->doLogin('test1','test');
        $list = self::$doctrine->getRepository('AppBundle:BookList')->findOneByName('list1');

        $formData = array(
            'list' => $list->getId(),
            'content' => 'Prueba comentario'
        );

        $this->client->request('POST', '/post_comment', $formData);
        $response = $this->client->getResponse();
        $this->assertEquals(
            201,
            $response->getStatusCode()
        );

        $responseContent = json_decode($this->client->getResponse()->getContent());

        $formData = array(
            'list' => $list->getId(),
            'content' => 'Prueba comentario',
            'parent' => $responseContent->id
        );

        $this->client->request('POST', '/post_comment', $formData);

        $this->assertEquals(
            201,
            $this->client->getResponse()->getStatusCode()
        );

        $this->client->request('GET', '/element_conversation/list/'.$list->getId());

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAuthorCommentSubmit()
    {
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $author = self::$doctrine->getRepository('AppBundle:Author')->findOneByName('author1');

        $formData = array(
            'author' => $author->getId(),
            'content' => 'Prueba comentario'
        );

        $this->client->request('POST', '/post_comment', $formData);
        $response = $this->client->getResponse();
        $this->assertEquals(
            201,
            $response->getStatusCode()
        );

        $responseContent = json_decode($this->client->getResponse()->getContent());

        $formData = array(
            'author' => $author->getId(),
            'content' => 'Prueba comentario',
            'parent' => $responseContent->id
        );

        $this->client->request('POST', '/post_comment', $formData);

        $this->assertEquals(
            201,
            $this->client->getResponse()->getStatusCode()
        );

        $this->client->request('GET', '/element_conversation/author/'.$author->getId());

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
