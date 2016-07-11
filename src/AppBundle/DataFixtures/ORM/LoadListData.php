<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookList;

class LoadListData extends AbstractFixture implements OrderedFixtureInterface
{    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $list = new BookList();
        $list->setName('list1');
        $list->setUser($this->getReference('user1'));
        $list->getBooks()->add($this->getReference('book1'));
        $list->getBooks()->add($this->getReference('book2'));
        $list->setPublicFlag(BookList::READ_PUBLIC);
        $list->getFollowers()->add($this->getReference('user2'));

        $this->addReference('list1', $list);
        $manager->persist($list);
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
