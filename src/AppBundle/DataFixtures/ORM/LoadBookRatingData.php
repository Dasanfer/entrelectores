<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\BookRating;

class LoadBookRatingData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $rating = new BookRating();
        $rating->setValue(9);
        $rating->setUser($this->getReference('user1'));
        $rating->setBook($this->getReference('book3'));
        $manager->persist($rating);

        $rating = new BookRating();
        $rating->setValue(9);
        $rating->setUser($this->getReference('user2'));
        $rating->setBook($this->getReference('book3'));
        $manager->persist($rating);

        $rating = new BookRating();
        $rating->setValue(9);
        $rating->setUser($this->getReference('user2'));
        $rating->setBook($this->getReference('book2'));
        $manager->persist($rating);

        $rating = new BookRating();
        $rating->setValue(9);
        $rating->setUser($this->getReference('user3'));
        $rating->setBook($this->getReference('book3'));
        $manager->persist($rating);

        $rating = new BookRating();
        $rating->setValue(9);
        $rating->setUser($this->getReference('user3'));
        $rating->setBook($this->getReference('book1'));
        $manager->persist($rating);

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
