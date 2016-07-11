<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Message;

class LoadMessageData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $user1 = $this->getReference('user1');
        $user3 = $this->getReference('user3');

        $message = new Message();
        $message->setTo($user1);
        $message->setFrom($user3);
        $message->setText("test message");
        $message->setReaded(new \DateTime());
        $manager->persist($message);

        $message = new Message();
        $message->setTo($user3);
        $message->setFrom($user1);
        $message->setText("test message");
        $manager->persist($message);

        $manager->flush();
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
