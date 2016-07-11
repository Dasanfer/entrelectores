<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{

    private $client = null;
    public static $container;
    public static $doctrine;

    public static $fixtures = array(
        'AppBundle\DataFixtures\ORM\LoadUserData',
        'AppBundle\DataFixtures\ORM\LoadBookData',
        'AppBundle\DataFixtures\ORM\LoadBookRatingData',
        'AppBundle\DataFixtures\ORM\LoadListData',
        'AppBundle\DataFixtures\ORM\LoadEventsData',
        'AppBundle\DataFixtures\ORM\LoadPollData'
    );

    public function testBooks()
    {
        $this->loadFixtures(self::$fixtures);
        $client = static::createClient();
        $crawler = $client->request('GET', '/libros');

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $crawler = $client->request('GET', '/libros/page/1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testBook()
    {
        $this->loadFixtures(self::$fixtures);
        $client = static::createClient();
        $crawler = $client->request('GET', '/libros/author1/test1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testAllGenres()
    {
        $this->loadFixtures(self::$fixtures);
        $client = static::createClient();
        $crawler = $client->request('GET', '/generos');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testGenre()
    {
        $this->loadFixtures(self::$fixtures);
        $client = static::createClient();
        $crawler = $client->request('GET', '/generos/genre1');

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $crawler = $client->request('GET', '/generos/genre1/page/1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testAuthor()
    {
        $this->loadFixtures(self::$fixtures);
        $client = static::createClient();

        $crawler = $client->request('GET', '/autores');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $crawler = $client->request('GET', '/autores/author1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testBookLooged()
    {

        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $this->doLogin('test1','test');
        $crawler = $this->client->request('GET', '/libros/author1/test1');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testHomeRegister(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/');
        $button = $crawler->selectButton('boton_registro');

        $form = $button->form(array(
            'small_user_registration[username]'  => 'registerTest',
            'small_user_registration[email]'  => 'registerTest@clouddistrict.com',
            'small_user_registration[plainPassword]' => 'test'
        ));
        $form['small_user_registration[dataConsent]']->tick();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/\/user_homepage$/', $this->client->getRequest()->getUri());
    }

    public function testHomeLogin(){
        $this->loadFixtures(self::$fixtures);
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('_submit')->form(array(
            '_username'  => 'test1',
            '_password'  => 'test',
        ));
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('div.logged')->count());
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        $firewall = 'main';
        $token = new UsernamePasswordToken('test1', null, $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
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
