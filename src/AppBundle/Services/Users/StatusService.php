<?php
namespace AppBundle\Services\Users;
use AppBundle\Entity\UserStatus;
use AppBundle\Entity\FollowEvent;
use AppBundle\Forms\UserStatusType;
use AppBundle\Services\Timeline\TimelineEvent;

class StatusService

{
    private $doctrine;
    private $eventDispatcher;

    public function __construct($doctrine,$eventDispatcher){
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function attachStatus($user,$status){
        $status->setUser($user);
        $event = new TimelineEvent($user,FollowEvent::status);
        $event->setUser($user);
        $event->setStatus($status);
        $this->eventDispatcher->dispatch('app.timeline_event',$event);
        $this->doctrine->getManager()->persist($status);
    }
}
