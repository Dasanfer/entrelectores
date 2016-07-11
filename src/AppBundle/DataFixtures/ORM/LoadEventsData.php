<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookList;
use AppBundle\Entity\FollowEvent;
use AppBundle\Entity\Comment;

class LoadEventsData extends AbstractFixture implements OrderedFixtureInterface
{    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $em = $manager;

        //get the elements
        $user = $this->getReference('user3');
        $book = $this->getReference('book1');
        $list = $this->getReference('list1');
        $author = $this->getReference('author1');

        //make some following events
        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($user);
        $followEvent->setBook($book);
        $followEvent->setType($followEvent::follow_book);
        $em->persist($followEvent);

        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($user);
        $followEvent->setList($list);
        $followEvent->setType($followEvent::follow_list);
        $em->persist($followEvent);

        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($user);
        $followEvent->setAuthor($author);
        $followEvent->setType($followEvent::follow_author);
        $em->persist($followEvent);

        //now some comments
        $comment = new Comment();
        $comment->setCreatedBy($user);
        $comment->setBook($book);
        $comment->setContent('-----');
        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($user);
        $followEvent->setComment($comment);
        $followEvent->setBook($book);
        $followEvent->setType($followEvent::comment);
        $em->persist($comment);
        $em->persist($followEvent);

        $comment = new Comment();
        $comment->setCreatedBy($user);
        $comment->setAuthor($author);
        $comment->setContent('-----');
        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($user);
        $followEvent->setComment($comment);
        $followEvent->setAuthor($author);
        $followEvent->setType($followEvent::author_comment);
        $em->persist($comment);
        $em->persist($followEvent);

        $comment = new Comment();
        $comment->setCreatedBy($user);
        $comment->setList($list);
        $comment->setContent('-----');
        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($user);
        $followEvent->setComment($comment);
        $followEvent->setList($list);
        $followEvent->setType($followEvent::list_comment);
        $em->persist($comment);
        $em->persist($followEvent);
        //end followings and comments now to user homepage
        $em->flush();

    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 30;
    }
}
