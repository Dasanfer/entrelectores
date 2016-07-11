<?php
namespace AppBundle\Services\Lists;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Forms\AddToListType;
use AppBundle\Entity\BookList;
use AppBundle\Entity\FollowEvent;
use AppBundle\Services\Timeline\TimelineEvent;

class ListService
{
    private $doctrine;
    private $formFactory;
    private $logger;
    private $serializer;
    private $eventDispatcher;

    public function __construct($doctrine,$formFactory,$logger,$serializer,$eventDispatcher){
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addToList($book,$list,$byUser){
        if(!$list->getBooks()->contains($book)
            && $this->checkWritePermissions($list,$byUser)){

            $list->getBooks()->add($book);
            $this->doctrine->getManager()->persist($list);

            if($list->getPublicFlag() > BookList::USER_PRIVATE){
                $event = new TimelineEvent($byUser,FollowEvent::book_add,$book,$list);
                $this->eventDispatcher->dispatch('app.timeline_event',$event);
            }
        }
        return true;
    }


    public function deleteList($list){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('e')
            ->from('AppBundle:FollowEvent','e')
            ->where($qb->expr()->eq('e.list',':list'));
        $qb->setParameter('list',$list);

        $events = $qb->getQuery()->getResult();
        foreach($events as $event){
            $em->remove($event);
        }

        $em->remove($list);
    }

    public function removeFromList($book,$list,$byUser){
        if($list->getBooks()->contains($book)
            && $this->checkWritePermissions($list,$byUser)){

            $list->getBooks()->removeElement($book);
            $this->doctrine->getManager()->persist($list);
            return true;
        }
        else
            return false;
    }

    public function checkReadingPermissions($list,$user){
        if($list->getUser() == $user)
            return true;

        if($list->getPublicFlag() == $list::READ_PUBLIC
            && $list->getFollowers()->contains($user))
            return true;

        return false;
    }

    public function checkWritePermissions($list,$user){
        if($list->getUser() == $user)
            return true;

        return false;
    }

    public function processAddition(Request $request,$user){
        $form = $this->formFactory->create(new AddToListType());
        $form->submit($request->request->all());
        $newList = false;
        if(!$form->isValid()){
            return false;
        }

        $data = $form->getData();

        if(!array_key_exists('list',$data) && !array_key_exists('newName',$data))
            return false;

        if(is_null($data['book']))
            return false;

        if(!is_null($data['newName'])){
            $list = new BookList();
            $list->setName($data['newName']);
            $list->setUser($user);
        } else {
            $list = $data['list'];
        }
        $result = $this->addToList($data['book'],$list,$user);

        if($result)
            return $list;
        else
            return false;
    }

    public function processRemove(Request $request,$user){
        $form = $this->formFactory->create(new AddToListType());
        $form->submit($request->request->all());
        $data = $form->getData();

        if(is_null($data['list']) || is_null($data['book']))
            return false;

        return $this->removeFromList($data['book'],$data['list'],$user);
    }

    public function booksInList($list,$offset = null,$count = null){
        $qb = $this->doctrine->getManager()->createQueryBuilder();

        $qb->select('b')->from('AppBundle:Book','b')->join('b.lists','l')
            ->where($qb->expr()->eq('l',':list'))
            ->orderBy('b.title');
        $qb->setParameter('list',$list);

        return $qb->getQuery()->setMaxResults($count)->setFirstResult($offset)->getResult();
    }
}
