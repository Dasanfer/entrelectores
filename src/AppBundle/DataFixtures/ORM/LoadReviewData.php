<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\Review;

class LoadReviewData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $review = new Review();
        $review->setTitle('Review1');
        $review->setText('Test review text');
        $review->setUser($this->getReference('user1'));
        $review->setBook($this->getReference('book3'));
        $review->setSpoiler(false);

        $manager->persist($review);
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
