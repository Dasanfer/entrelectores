<?php
namespace AppBundle\Services\Timeline;
use AppBundle\Services\Timeline\TimelineEvent;
use AppBundle\Entity\FollowEvent;

class EventListener
{
    private $logger;
    private $doctrine;

    public function __construct($doctrine,$logger){
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    public function onTimelineEvent(TimelineEvent $event)
    {
        $createdBy = $event->getCreatedBy();
        $this->logger->debug('TIMELINE_EVENT '.$createdBy->getName().' '.$event->getType());

        $followEvent = new FollowEvent();
        $followEvent->setCreatedBy($createdBy);
        $followEvent->setBook($event->getBook());
        $followEvent->setAuthor($event->getAuthor());
        $followEvent->setList($event->getList());
        $followEvent->setUser($event->getUser());
        $followEvent->setReview($event->getReview());
        $followEvent->setType($event->getType());
        $followEvent->setComment($event->getComment());
        $followEvent->setStatus($event->getStatus());

        $this->doctrine->getManager()->persist($followEvent);
    }
}
