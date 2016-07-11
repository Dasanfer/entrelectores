<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    public $container;
    public function setContainer(ContainerInterface $container = null){
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->addUser(1)->setPublicProfile(true));
        $manager->persist($this->addUser(2)->setPublicProfile(true));
        $manager->persist($this->addUser(3));

        $this->getReference('user1')->getFollowers()->add($this->getReference('user3'));
        $this->getReference('user3')->getFollowers()->add($this->getReference('user1'));
        $manager->flush();
    }

    private function addUser($number){
        $userManager = $this->container->get('fos_user.user_manager');
        $user = new User();
        $user->setUsername('test'.$number);
        $user->setUsernameCanonical('test'.$number);
        $user->setEmail('test'.$number.'@clouddistrict.com');
        $user->setEmailCanonical('test'.$number.'@clouddistrict.com');
        $user->setName('test'.$number.'@clouddistrict.com');
        $user->setPassword('test');
        $user->setPlainPassword('test');
        $user->setEnabled(true);
        $user->setPublicProfile(false);
        $this->addReference('user'.$number, $user);
        $userManager->updateUser($user, true);
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
