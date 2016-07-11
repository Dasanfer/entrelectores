<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use AppBundle\Entity\Poll;
use AppBundle\Entity\PollOption;

class LoadPollData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $poll = new Poll();
        $poll->setTitle('title_poll');
        $poll->setSlug('title-poll');
        $poll->setQuestion('Question');

        $pollOption1 = new PollOption();
        $pollOption1->setTitle('title1')->setPoll($poll);
        $pollOption2 = new PollOption();
        $pollOption2->setTitle('title2')->setPoll($poll);

        $manager->persist($pollOption1);
        $manager->persist($pollOption2);
        $manager->persist($poll);

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
